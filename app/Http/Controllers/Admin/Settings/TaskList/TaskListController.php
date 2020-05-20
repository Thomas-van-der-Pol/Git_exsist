<?php

namespace App\Http\Controllers\Admin\Settings\TaskList;

use App\Libraries\Core\DropdownvalueUtils;
use Illuminate\Http\Request;
use KJ\Core\controllers\AdminBaseController;
use KJ\Core\libraries\SessionUtils;
use KJLocalization;

class TaskListController extends AdminBaseController
{
    protected $model = 'App\Models\Admin\Task\TaskList';
    protected $mainViewName = 'admin.settings.tasklist.main';

    protected $detailViewName = 'admin.settings.tasklist.detail';
    protected $detailScreenFolder = 'admin.settings.tasklist.detail_screens';

    protected $allColumns = ['ID', 'NAME', 'ACTIVE'];

    protected $datatableDefaultSort = array(
        [
            'field' => 'Name',
            'sort'  => 'ASC'
        ]
    );

    protected $saveValidation = [
        'NAME' => 'required'
    ];

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
                'default' => SessionUtils::getSession('ADM_TASKLIST', 'ADM_FILTER_TASKLIST_STATUS', 1)
            )]
        );

        return parent::allDatatable($request);
    }

    protected function beforeDetail(int $ID, $item)
    {
      $status = DropdownvalueUtils::getStatusDropdown(false);

      $bindings = array(
          ['status', $status]
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


}