<?php

namespace App\Http\Controllers\Admin\Settings\Workflow;

use App\Libraries\Core\DropdownvalueUtils;
use App\Models\Core\WorkflowState;
use App\Models\Core\WorkflowStateType;
use Illuminate\Http\Request;
use KJ\Core\controllers\AdminBaseController;
use KJLocalization;

class WorkflowTypeController extends AdminBaseController
{
    protected $model = 'App\Models\Core\WorkflowStateType';

    protected $mainViewName = 'admin/settings/workflow/main';

    protected $allColumns = [
        'ID',
        'ACTIVE',
        'FIXED',
        'DESCRIPTION',
        'FK_CORE_DROPDOWNVALUE'
    ];

    protected $whereClause = [
        ['FIXED', false]
    ];

    protected $datatableFilter = array(
        ['ACTIVE', array(
            'param' => 'ACTIVE',
            'default' => true
        )]
    );

    protected $datatableDefaultSort = array(
        [
            'field' => 'DESCRIPTION',
            'sort'  => 'ASC'
        ]
    );

    protected $detailScreenFolder = 'admin.settings.workflow.detail_screens';
    protected $detailViewName = 'admin.settings.workflow.detail';

    protected function beforeDatatable($datatable)
    {
        $datatable->addColumn('PROJECT_TYPE', function(WorkflowStateType $workflowStateType) {
            return $workflowStateType->project_type ? $workflowStateType->project_type->getValueAttribute() : '';
        });
    }

    public function beforeDetailScreen(int $id, $item, $screen)
    {
        $bindings = [];

        $none = ['' => KJLocalization::translate('Algemeen', 'Niets geselecteerd', 'Niets geselecteerd') . '..'];

        switch ($screen) {
            case 'default':
                $project_types = DropdownvalueUtils::getDropdown(config('dropdown_type.TYPE_PROJECTTYPE'));

                $workflow_statesOri = WorkflowState::where(['ACTIVE' => true, 'FK_CORE_WORKFLOWSTATETYPE' => $id])->pluck('DESCRIPTION', 'ID')->toArray();
                $workflow_states = $none + $workflow_statesOri;

                $bindings = array_merge($bindings, [
                    ['project_types', $project_types],
                    ['workflow_states', $workflow_states]
                ]);
                break;
        }

        return $bindings;
    }

    public function save(Request $request)
    {
        $this->saveExtraValues = [
            'FIXED' => false
        ];

        return parent::save($request);
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

    public function allByProjectType(int $ID)
    {
        $none = ['' => KJLocalization::translate('Algemeen', 'Niets geselecteerd', 'Niets geselecteerd') . '..'];

        $itemsOri = WorkflowStateType::where('ACTIVE', true)
            ->where('FK_CORE_DROPDOWNVALUE', $ID)
            ->pluck('DESCRIPTION', 'ID');

        $items = $none + $itemsOri->toArray();

        return response()->json([
            'items' => $items
        ], 200);
    }
}