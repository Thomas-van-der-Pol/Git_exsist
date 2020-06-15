<?php

namespace App\Http\Controllers\Admin\CRM;

use App\Libraries\Core\DropdownvalueUtils;
use App\Models\Admin\Project\Project;
use Illuminate\Support\Facades\Auth;
use KJ\Core\controllers\AdminBaseController;
use Illuminate\Http\Request;
use KJ\Core\libraries\SessionUtils;
use KJLocalization;

class ContactController extends AdminBaseController {

    protected $model = 'App\Models\Admin\CRM\Contact';

    protected $allColumns = ['ID', 'ACTIVE', 'FULLNAME', 'LASTNAME', 'FIRSTNAME', 'CELLPHONENUMBER' ,'EMAILADDRESS', 'PHONENUMBER', 'FK_CORE_DROPDOWNVALUE_SALUTATION', 'FK_CRM_RELATION'];

    protected $joinClause = [
        [
            'TABLE' => 'CRM_RELATION',
            'PRIMARY_FIELD' => 'ID',
            'FOREIGN_FIELD' => 'CRM_CONTACT.FK_CRM_RELATION'
        ]
    ];

    protected $datatableDefaultSort = array(
        [
            'field' => 'FULLNAME',
            'sort'  => 'ASC'
        ]
    );

    protected $mainViewName = 'admin.crm.contact.main';

    protected $exceptAuthorization = ['indexModal', 'allDatatable'];

    protected function authorizeRequest($method, $parameters)
    {
        return Auth::guard()->user()->hasPermission(config('permission.CRM'));
    }

    protected function beforeIndex()
    {
        $status = DropdownvalueUtils::getStatusDropdown(false);

        $bindings = [
            ['status', $status]
        ];

        return $bindings;
    }

    protected function getSortField($sortField)
    {
        if ($sortField === 'RELATION') {
            return 'CRM_RELATION.NAME';
        } else {
            return parent::getSortField($sortField);
        }
    }

    protected function beforeDatatable($datatable)
    {
        $datatable->addColumn('RELATION', function($item) {
            return ( $item ? $item->relation->title : '' );
        });
    }

    public function indexModal()
    {
        $view = view('admin.crm.contact.modal');

        return response()->json([
            'viewDetail' => $view->render()
        ]);
    }

    public function allDatatable(Request $request)
    {
        $modal = (($request->get('modal') ?? false) == '1');
        $this->whereInClause = [];
        $this->datatableFilter = array(
            ['ID, FIRSTNAME, LASTNAME, PHONENUMBER, CELLPHONENUMBER, EMAILADDRESS, FULLNAME, CRM_RELATION.NAME', array(
                'param' =>  $modal ? 'ADM_CRM_CONTACT_MODAL_FILTER_SEARCH': 'ADM_CRM_CONTACT_FILTER_SEARCH',
                'operation' => 'like',
                'default' => $modal ? \KJ\Core\libraries\SessionUtils::getSession('ADM_CRM', 'ADM_CRM_CONTACT_MODAL_FILTER_SEARCH', '') : SessionUtils::getSession('ADM_CRM', 'ADM_CRM_CONTACT_FILTER_SEARCH', '')
            )]
        );

        if ($modal) {
            $this->whereInClause = array(
                ['CRM_RELATION.FK_CORE_DROPDOWNVALUE_RELATIONTYPE', [config('relation_type.WERKGEVER'), config('relation_type.WERKNEMER')]],
                ['CRM_RELATION.ACTIVE', [true]],
            );
        }

        return parent::allDatatable($request);
    }

    protected function allInternalModifyItems(&$items)
    {
        $active = (isset(request('query')['ACTIVE']) ? request('query')['ACTIVE'] : SessionUtils::getSession('ADM_CRM', 'ADM_FILTER_CONTACT_STATUS', 1));

        if(\request('modal') != '1') {
            if ($active == true) {
                $items->where('CRM_CONTACT.ACTIVE', $active);
                $items->where('CRM_RELATION.ACTIVE', $active);
            } else {
                $items->where(function ($query) use ($active) {
                    $query->where('CRM_CONTACT.ACTIVE', $active);
                    $query->orWhere('CRM_RELATION.ACTIVE', $active);
                });
            }
        } else {
            $items->where(function ($query) {
                $query->where('CRM_CONTACT.ACTIVE', true);
                $query->orWhere('CRM_RELATION.ACTIVE', true);
            });
        }
    }

    public function detailRelation(Request $request, int $ID) {
        $contact = $this->find($ID);

        if ($contact->relation->ID > 0) {
            return redirect('/admin/crm/relation/detail/' . $contact->relation->ID);
        }

        return null;
    }
}