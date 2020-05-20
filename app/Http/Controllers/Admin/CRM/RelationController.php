<?php

namespace App\Http\Controllers\Admin\CRM;

use App\Libraries\Core\DropdownvalueUtils;
use App\Models\Admin\Core\Label;
use App\Models\Admin\CRM\Contact;
use App\Models\Admin\CRM\Relation;
use App\Models\Admin\Finance\CollectInterval;
use App\Models\Admin\Project\Product;
use App\Models\Admin\Project\Project;
use Illuminate\Support\Facades\Auth;
use KJ\Core\controllers\AdminBaseController;
use Illuminate\Http\Request;
use KJLocalization;

class RelationController extends AdminBaseController {

    protected $model = 'App\Models\Admin\CRM\Relation';

    protected $mainViewName = 'admin.crm.relation.main';

    protected $allColumns = ['ID', 'ACTIVE', 'NAME', 'EMAILADDRESS', 'PHONENUMBER', 'FK_CORE_DROPDOWNVALUE_RELATIONTYPE'];

    protected $joinClause = [
        [
            'TABLE' => 'CORE_DROPDOWNVALUE',
            'PRIMARY_FIELD' => 'ID',
            'FOREIGN_FIELD' => 'FK_CORE_DROPDOWNVALUE_RELATIONTYPE'
        ],
        [
            'TABLE' => 'CORE_TRANSLATION',
            'PRIMARY_FIELD' => 'FK_CORE_TRANSLATION_KEY',
            'FOREIGN_FIELD' => 'CORE_DROPDOWNVALUE.TL_VALUE',
            'TYPE' => 'LEFT'
        ]
    ];

    protected $datatableDefaultSort = array(
        [
            'field' => 'NAME',
            'sort'  => 'ASC'
        ]
    );

    protected $detailScreenFolder = 'admin.crm.relation.detail_screens';
    protected $detailViewName = 'admin.crm.relation.detail';

    protected $saveUnsetValues = [
        'RATE_KM_READ'
    ];

    protected $exceptAuthorization = ['indexModal', 'allDatatable'];

    protected function authorizeRequest($method, $parameters)
    {
        return Auth::guard()->user()->hasPermission(config('permission.CRM'));
    }

    protected function beforeIndex()
    {
        $status = DropdownvalueUtils::getStatusDropdown(false);
        $relationtypes = DropdownvalueUtils::getDropdown(config('dropdown_type.TYPE_RELATIONTYPE'),true);

        $bindings = array(
            ['status', $status],
            ['relationtypes', $relationtypes]
        );

        return $bindings;
    }

    protected function getSortField($sortField)
    {
        if ($sortField === 'RELATION_TYPE') {
            return 'CORE_TRANSLATION.TEXT';
        } else {
            return parent::getSortField($sortField);
        }
    }

    protected function beforeDatatable($datatable)
    {
        $datatable->addColumn('RELATION_TYPE', function(Relation $relation) {
            return $relation->type->Value ?? '';
        });
    }

    public function indexModal()
    {
        $view = view('admin.crm.relation.modal');
        $type = request('type');

        if($type) {
            $view->with('type', $type);
        }
        return response()->json([
            'viewDetail' => $view->render()
        ]);
    }

    public function allDatatable(Request $request)
    {
        $type = request('type');
        $localeId = config('app.locale_id') ? config('app.locale_id') : config('language.defaultLangID');

        $this->whereClause = [
            ['CORE_TRANSLATION.FK_CORE_LANGUAGE', $localeId]
        ];

        $this->datatableFilter = array(
            ['ID, NAME, EMAILADDRESS ', array(
                'param' => 'ADM_RELATION_FILTER_SEARCH',
                'operation' => 'like',
                'default' => \KJ\Core\libraries\SessionUtils::getSession('ADM_RELATION', 'ADM_RELATION_FILTER_SEARCH', '')
            )],
            ['ACTIVE', array(
                'param' => 'ACTIVE',
                'default' => \KJ\Core\libraries\SessionUtils::getSession('ADM_RELATION', 'ADM_FILTER_RELATION_STATUS', 1)
            )],
            ['FK_CORE_DROPDOWNVALUE_RELATIONTYPE', array(
                'param' => 'FK_CORE_DROPDOWNVALUE_RELATIONTYPE',
                'default' => $type? $type: \KJ\Core\libraries\SessionUtils::getSession('ADM_RELATION', 'ADM_FILTER_RELATION_TYPE',  '')
            )]

        );

        return parent::allDatatable($request);
    }

    public function beforeDetailScreen(int $id, $item, $screen)
    {
        $none = ['' => KJLocalization::translate('Algemeen', 'Niets geselecteerd', 'Niets geselecteerd') . '..'];

        $bindings = [];

        switch ($screen) {
            case 'default':
                $contactpersonen    = Contact::all()->where('ACTIVE', true)->where('FK_CRM_RELATION', $id)->pluck('FULLNAME', 'ID')->sortBy('LASTNAME');
                $contactpersonen    = $none + $contactpersonen->toArray();
                $relationtypes      = DropdownvalueUtils::getDropdown(config('dropdown_type.TYPE_RELATIONTYPE'),FALSE);

                $labels = Label::where('ACTIVE', true);
                $default_label = null;
                if ($labels->count() == 1) {
                    $default_label = $labels->first()->ID;
                }
                $labels = $none + $labels->pluck('DESCRIPTION', 'ID')->toArray();

                $bindings = array_merge($bindings, [
                    ['contactpersonen', $contactpersonen],
                    ['relationtypes', $relationtypes],
                    ['labels', $labels],
                    ['default_label', $default_label]
                ]);
                break;

            case 'financial_details':
                $collectIntervalOri = CollectInterval::all()->where('ACTIVE', true)->sortBy('SEQUENCE')->pluck('title', 'ID');
                $collectInterval = $none + $collectIntervalOri->toArray();

                $bindings = array_merge($bindings, [
                    ['collectInterval', $collectInterval]
                ]);
                break;

            case 'contacts':
            case 'projects':
                $status = DropdownvalueUtils::getStatusDropdown(false);

                $bindings = array_merge($bindings, [
                    ['status', $status]
                ]);
                break;
        }

        return $bindings;
    }

    public function save(Request $request)
    {
        // Validate if relation isn't linked at a project
        if ($request->get('ID') != $this->newRecordID) {
            $relation = $this->find($request->get('ID'));

            // Relation type changed
            if (($request->get('FK_CORE_DROPDOWNVALUE_RELATIONTYPE') != null) && ($relation->FK_CORE_DROPDOWNVALUE_RELATIONTYPE != $request->get('FK_CORE_DROPDOWNVALUE_RELATIONTYPE'))) {
                // Linked at project
                $linked = (Project::where('FK_CRM_RELATION_REFERRER', $relation->ID)
                        ->orWhere('FK_CRM_RELATION_EMPLOYER', $relation->ID)
                        ->count() > 0);

                // Linked at project products
                if (!$linked) {
                    $linked = (Product::where('FK_CRM_RELATION', $relation->ID)->count() > 0);
                }

                // Result
                if ($linked) {
                    return response()->json([
                        'success' => false,
                        'message' => KJLocalization::translate('Admin - CRM', 'Type relatie kan niet aangepast worden omdat deze relatie al in een dossier is gekoppeld', 'Type relatie kan niet aangepast worden omdat deze relatie al in een dossier is gekoppeld')
                    ]);
                }
            }
        }

        return parent::save($request);
    }

    protected function afterSave($item, $originalItem, Request $request, &$response)
    {
        if($request->get('ID') == $this->newRecordID) {
            // Save default label administration
            $item->NUMBER_DEBTOR = $item->label->NEXT_DEBTOR_NUMBER;
            $item->INVOICE_ELECTRONIC = $item->label->DEFAULT_DIGITAL_INVOICE;
            $item->PAYMENTTERM_DAY = $item->label->DEFAULT_PAYMENTTERM_DAY;
            $item->RATE_KM = $item->label->DEFAULT_RATE_KM;
            $item->FK_FINANCE_INVOICE_COLLECT_INTERVAL = 1;
            $item->save();

            $item->label->getNewDebtorNumber();
        }
    }

    public function delete(int $id)
    {
        $item = $this->find($id);

        if ($item) {
            if ($item->ACTIVE) {
                $status = 'gearchiveerd';
            } else {
                $status = 'geactiveerd';
            }

            $item->ACTIVE = !$item->ACTIVE;
            $result = $item->save();

            return response()->json([
                'success' => $result,
                'message' => KJLocalization::translate('Algemeen', 'Item kon niet worden ' . $status, 'Item kon niet worden ' . $status)
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => KJLocalization::translate('Algemeen', 'Item niet (meer) gevonden', 'Item niet (meer) gevonden')
            ]);
        }
    }

    public function generateDebtornumber(int $ID)
    {
        $item = $this->find($ID);
        $item->NUMBER_DEBTOR = null;
        $item->save();

        $item->generateDebtornumber();

        return response()->json([
            'success' => true
        ]);
    }

    public function generateCreditornumber(int $ID)
    {
        $item = $this->find($ID);
        $item->NUMBER_CREDITOR = null;
        $item->save();

        $item->generateCreditornumber();

        return response()->json([
            'success' => true
        ]);
    }

}