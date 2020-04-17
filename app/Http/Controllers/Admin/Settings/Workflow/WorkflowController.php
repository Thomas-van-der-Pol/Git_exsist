<?php

namespace App\Http\Controllers\Admin\Settings\Workflow;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use KJ\Core\controllers\AdminBaseController;
use KJLocalization;

class WorkflowController extends AdminBaseController
{
    protected $model = 'App\Models\Core\WorkflowState';

    protected $allColumns = [
        'ID',
        'ACTIVE',
        'SEQUENCE',
        'DESCRIPTION',
        'ACTION_DESCRIPTION'
    ];

    protected $datatableDefaultSort = array(
        [
            'field' => 'SEQUENCE',
            'sort'  => 'ASC'
        ]
    );

    protected $detailViewName = 'admin/settings/workflow/state/detail';

    protected $saveParentIDField = 'FK_CORE_WORKFLOWSTATETYPE';

    protected function authorizeRequest($method, $parameters)
    {
        return Auth::guard()->user()->hasPermission(config('permission.INSTELLINGEN'));
    }

    public function allByTypeDatatable(Request $request, int $ID)
    {
        $this->whereClause = array(
            ['FK_CORE_WORKFLOWSTATETYPE', $ID],
            ['ACTIVE', true]
        );

        return parent::allDatatable($request);
    }

    public function save(Request $request)
    {
        $this->saveExtraValues = [];

        if ($request->get('ID') == $this->newRecordID) {
            // Sequence
            $nextSequence = collect(DB::select('EXEC [SEQUENCE_NEXT] @TABLE = ?, @WHERE_FIELDS = ?, @WHERE_VALUES = ?', [
                'CORE_WORKFLOWSTATE',
                'FK_CORE_WORKFLOWSTATETYPE',
                ($request->get('PARENTID') ?? 0),
            ]))->first();

            $this->saveExtraValues = [
                'SEQUENCE' => $nextSequence->SEQUENCE
            ];
        }

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

    public function sequence(Request $request, int $ID)
    {
        $result = DB::update('EXEC [SEQUENCE_SET] @TABLE = ?, @WHERE_FIELDS = ?, @WHERE_VALUES = ?, @ID = ?, @MUTATION = ?, @ID_FIELD = ?, @SEQUENCE_FIELD = ?', [
            'CORE_WORKFLOWSTATE',
            'ACTIVE, FK_CORE_WORKFLOWSTATETYPE',
            '1,'. $ID,
            $request->get('id'),
            $request->get('mutation'),
            'ID',
            'SEQUENCE'
        ]);

        return response()->json([
            'success'=> ( $result != null ),
        ], 200);
    }
}