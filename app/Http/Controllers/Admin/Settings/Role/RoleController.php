<?php

namespace App\Http\Controllers\Admin\Settings\Role;

use App\Libraries\Core\DropdownvalueUtils;
use App\Models\Admin\Core\Permission;
use KJ\Core\controllers\AdminBaseController;
use App\Models\Core\Role;
use App\Models\Core\RolePermission;
use Illuminate\Http\Request;
use KJ\Core\libraries\SessionUtils;
use KJLocalization;

class RoleController extends AdminBaseController
{
    protected $model        = 'App\Models\Admin\Core\Role';

    protected $mainViewName = 'admin/settings/role/main';

    protected $allColumns = [
        'ID',
        'ACTIVE',
        'DESCRIPTION'
    ];

    protected $datatableDefaultSort = array(
        [
            'field' => 'DESCRIPTION',
            'sort'  => 'ASC'
        ]
    );

    protected $detailViewName = 'admin/settings/role/detail';

    protected $saveUnsetValues = [
        'PERMISSIONS'
    ];

//    protected function authorizeRequest($method, $parameters)
//    {
//        return Auth::guard()->user()->hasPermission(config('permission.SETTINGS_ROLE_PERMISSION'));
//    }

    protected function beforeIndex()
    {
        $status = DropdownvalueUtils::getStatusDropdown(false);

        $bindings = array(
            ['status', $status]
        );

        return $bindings;
    }

    public function allDatatable(Request $request)
    {
        $this->datatableFilter = array(
            ['ACTIVE', array(
                'param' => 'ACTIVE',
                'default' => SessionUtils::getSession('ADM_ROLE', 'ADM_FILTER_ROLE_STATUS', 1)
            )],
            ['DESCRIPTION', array(
                'param' => 'ADM_FILTER_ROLE',
                'operation' => 'like',
                'default' => SessionUtils::getSession('ADM_ROLE', 'ADM_FILTER_ROLE', '')
            )]
        );

        return parent::allDatatable($request);
    }

    protected function beforeDetail(int $ID, $item)
    {
        $permissions        = Permission::where('ACTIVE', true)->orderBy('DESCRIPTION')->get();
        $rolepermissions    = \App\Models\Admin\Core\RolePermission::where('FK_CORE_ROLE', $ID)->pluck('FK_CORE_PERMISSION');

        $bindings = array(
            ['permissions', $permissions],
            ['rolepermissions', $rolepermissions]
        );

        return $bindings;
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

    protected function afterSave($item, $originalItem, Request $request, &$response)
    {
        //0. Let op belangrijk om te refreshen omdat er translations aangemaakt zijn
        $item->refresh();

        //1 Rechten opslaan
        \App\Models\Admin\Core\RolePermission::where('FK_CORE_ROLE', $item->ID)->delete();
        $permissions = $request->input('PERMISSIONS');
        if($permissions) {
            foreach($permissions as $permissionID) {
                if ($permissionID > 0) {
                    $newRolePermission =  new \App\Models\Admin\Core\RolePermission;
                    $newRolePermission->FK_CORE_ROLE = $item->ID;
                    $newRolePermission->FK_CORE_PERMISSION = $permissionID;
                    $newRolePermission->save();
                }
            }
        }
    }
}