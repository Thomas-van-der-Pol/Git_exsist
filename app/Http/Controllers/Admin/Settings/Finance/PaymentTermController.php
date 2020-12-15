<?php

namespace App\Http\Controllers\Admin\Settings\Finance;

use App\Models\Admin\Finance\PaymentTerm;
use Collective\Html\FormFacade as Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use KJ\Core\controllers\AdminBaseController;
use KJLocalization;

class PaymentTermController extends AdminBaseController
{
    protected $model = 'App\Models\Admin\Finance\PaymentTerm';

    protected $allColumns = ['ID', 'ACTIVE', 'DESCRIPTION', 'AMOUNT_DAYS', 'CODE', 'DEFAULT'];

    protected $detailViewName = 'admin.settings.finance.payment_term.detail';

    protected $saveParentIDField = 'FK_CORE_LABEL';

    protected function authorizeRequest($method, $parameters)
    {
        return Auth::guard()->user()->hasPermission(config('permission.FACTURATIE'));
    }

    public function save(Request $request)
    {
        $id = $request->get('ID');

        // Validate default payment term
        $default = (int) ($request->get('DEFAULT') ? $request->get('DEFAULT') : '');
        if ($default != '') {
            if (!PaymentTerm::isDefaultValid($id)) {
                return response()->json([
                    'message' => KJLocalization::translate('Admin - Financieel', 'Er is al een standaard betalingsconditie in gebruik', 'Er is al een standaard betalingsconditie in gebruik'),
                    'success'=> false
                ], 200);
            }
        }

        return parent::save($request);
    }

    protected function beforeDatatable($datatable)
    {
        $datatable->addColumn('DEFAULT_FORMATTED', function ($item) {
            return new HtmlString('<label class="kt-checkbox default-label" style="margin-bottom: 10px !important;">
                <input '.($item->DEFAULT == 1 ? 'checked' : '').' disabled type="checkbox">
                <span></span>
            </label>');
        });
    }

    public function allByLabelDatatable(Request $request, int $id)
    {
        $this->whereClause = [
            ['FK_CORE_LABEL', $id],
            ['ACTIVE', true]
        ];

        $this->datatableDefaultSort = [
            [
                'field' => 'AMOUNT_DAYS',
                'sort' => 'ASC'
            ]
        ];

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