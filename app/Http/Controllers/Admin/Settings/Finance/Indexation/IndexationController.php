<?php

namespace App\Http\Controllers\Admin\Settings\Finance\Indexation;

use Illuminate\Support\Facades\Auth;
use KJ\Core\controllers\AdminBaseController;
use Illuminate\Http\Request;
use KJLocalization;

class IndexationController extends AdminBaseController
{
    protected $model = 'App\Models\Admin\Finance\Indexation';

    protected $allColumns = ['ID', 'ACTIVE', 'DESCRIPTION', 'PERCENTAGE', 'DONT_APPLY_INDEXATION'];

    protected $datatableDefaultSort = array(
        [
            'field' => 'DESCRIPTION',
            'sort'  => 'ASC'
        ]
    );

    protected $detailViewName = 'admin.settings.finance.indexation.detail';

    protected function authorizeRequest($method, $parameters)
    {
        return Auth::guard()->user()->hasPermission(config('permission.FACTURATIE'));
    }

    protected $whereClause = [
        ['ACTIVE', true]
    ];

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