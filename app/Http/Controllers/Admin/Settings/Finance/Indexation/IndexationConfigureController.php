<?php

namespace App\Http\Controllers\Admin\Settings\Finance\Indexation;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use KJ\Core\controllers\AdminBaseController;
use Illuminate\Http\Request;
use KJLocalization;

class IndexationConfigureController extends AdminBaseController
{
    protected $mainViewName = 'admin.settings.finance.indexation.main';

    protected function authorizeRequest($method, $parameters)
    {
        return Auth::guard()->user()->hasPermission(config('permission.FACTURATIE'));
    }

    public function retrieveIndex(Request $request)
    {
        $update = ( $request->get('update') ?? 0 );

        $items = collect(DB::select('EXEC [FINANCE_INDEX] ?', [
            $update
        ]));

        $view = view('admin.settings.finance.indexation.items')
            ->with('items', $items);

        return response()->json([
            'viewDetail' => $view->render()
        ]);
    }
}