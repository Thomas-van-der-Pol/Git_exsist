<?php

namespace App\Http\Controllers\Admin\CRM;

use App\Libraries\Core\DropdownvalueUtils;
use App\Models\Admin\Core\Label;
use App\Models\Admin\CRM\Contact;
use App\Models\Admin\CRM\Relation;
use App\Models\Admin\Finance\CollectInterval;
use Illuminate\Support\Facades\Auth;
use KJ\Core\controllers\AdminBaseController;
use Illuminate\Http\Request;
use KJLocalization;

class RelationController extends AdminBaseController {

    protected $model = 'App\Models\Admin\CRM\Relation';

    protected $mainViewName = 'admin.crm.relation.main';

    protected $allColumns = ['ID', 'ACTIVE', 'NAME', 'EMAILADDRESS', 'FK_CORE_DROPDOWNVALUE_RELATIONTYPE'];

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

        $this->datatableFilter = array(
            ['ID, NAME, EMAILADDRESS ', array(
                'param' => 'ADM_RELATION_FILTER_SEARCH',
                'operation' => 'like',
                'default' => \KJ\Core\libraries\SessionUtils::getSession('ADM_RELATION', 'ADM_RELATION_FILTER_SEARCH', '')
            )],
            ['ACTIVE', array(
                'param' => 'ACTIVE',
                'default' => true
            )],
            ['FK_CORE_DROPDOWNVALUE_RELATIONTYPE', array(
                'param' => 'FK_CORE_DROPDOWNVALUE_RELATIONTYPE',
                'default' => $type? $type: \KJ\Core\libraries\SessionUtils::getSession('ADM_RELATION', 'ADM_FILTER_RELATION_TYPE',  '')
            )]

        );

        return parent::allDatatable($request);
    }

    /**
     * MATERIAL THEME UITBREIDING
     */
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