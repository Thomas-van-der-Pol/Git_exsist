<?php

namespace App\Http\Controllers\Admin\Settings\Finance;

use Illuminate\Support\Facades\Auth;
use KJ\Core\controllers\AdminBaseController;
use Illuminate\Http\Request;
use KJLocalization;

class LedgerController extends AdminBaseController
{
    protected $model = 'App\Models\Admin\Finance\Ledger';

    protected $allColumns = ['ID', 'ACTIVE', 'ACCOUNT', 'DESCRIPTION'];

    protected $datatableDefaultSort = array(
        [
            'field' => 'ACCOUNT',
            'sort'  => 'ASC'
        ],
        [
            'field' => 'DESCRIPTION',
            'sort'  => 'ASC'
        ]
    );

    protected $detailViewName = 'admin.settings.finance.ledger.detail';

    protected $saveParentIDField = 'FK_CORE_LABEL';

    protected function authorizeRequest($method, $parameters)
    {
        return Auth::guard()->user()->hasPermission(config('permission.FACTURATIE'));
    }

    public function allByLabelDatatable(Request $request, int $ID)
    {
        $this->whereClause = array(
            ['FK_CORE_LABEL', $ID],
            ['ACTIVE', true]
        );

        return parent::allDatatable($request);
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