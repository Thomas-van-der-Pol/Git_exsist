<?php

namespace App\Http\Controllers\Admin\Finance\Exact;

use App\Libraries\Admin\ExactUtils;
use Illuminate\Http\Request;

class ExactResource extends ExactBaseResource
{
    protected $exceptGuard = ['exactRedirect'];

    protected $mainViewName = 'admin.finance.exact.main';

    protected $detailScreenFolder = 'admin.finance.exact.overview_screens';

    protected function beforeIndex()
    {
        $this->doConnect();
    }

    public function detailScreen(Request $request) {
        if ($this->detailScreenFolder == '') {
            abort(400, 'Geen detail screen folder opgegeven! Vul variabele detailScreenFolder.');
        }

        $screen = $request->get('SCREEN');
        $type = $request->get('type');

        $view = view($this->detailScreenFolder.'.'.$screen)
            ->with('type', $type);

        return response()->json([
            'success' => true,
            'type' => $type,
            'view' => $view->render()
        ]);
    }

    public function exactRedirect(Request $request)
    {
        return ExactUtils::redirect($request);
    }

}