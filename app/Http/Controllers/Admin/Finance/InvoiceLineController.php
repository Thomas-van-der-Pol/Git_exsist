<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Models\Admin\Finance\InvoiceLine;
use App\Models\Admin\Finance\Ledger;
use App\Models\Admin\Finance\VAT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use KJ\Core\controllers\AdminBaseController;
use KJLocalization;

class InvoiceLineController extends AdminBaseController {

    protected $model = 'App\Models\Admin\Finance\InvoiceLine';

    protected $allColumns = ['ID', 'FK_FINANCE_VAT', 'FK_FINANCE_LEDGER', 'ACTIVE', 'QUANTITY', 'PRICE', 'PRICE_INCVAT', 'DESCRIPTION', 'PRICE_TOTAL', 'PRICE_TOTAL_INCVAT', 'STARTDATE', 'ENDDATE', 'QUANTITY_MONTH'];

    protected $datatableDefaultSort = array(
        [
            'field' => 'DESCRIPTION',
            'sort'  => 'ASC'
        ]
    );

    protected $detailViewName = 'admin.finance.invoice.line.detail';

    protected $saveValidation = [
        'PARENTID' => 'required',
        'QUANTITY' => 'required',
        'DESCRIPTION' => 'required',
        'PRICE' => 'required',
        'FK_FINANCE_VAT' => 'required',
        'FK_FINANCE_LEDGER' => 'required'
    ];

    protected $saveParentIDField = 'FK_FINANCE_INVOICE';

    protected function authorizeRequest($method, $parameters)
    {
        return Auth::guard()->user()->hasPermission(config('permission.FACTURATIE'));
    }

    protected function beforeDatatable($datatable)
    {
        $datatable->addColumn('QUANTITY_FORMATTED', function(InvoiceLine $invoiceLine) {
                return $invoiceLine->QuantityFormatted;
            })
            ->addColumn('PRICE_FORMATTED', function(InvoiceLine $invoiceLine) {
                return $invoiceLine->PriceFormatted;
            })
            ->addColumn('PRICE_INCVAT_FORMATTED', function(InvoiceLine $invoiceLine) {
                return $invoiceLine->PriceIncVatFormatted;
            })
            ->addColumn('PRICETOTAL_FORMATTED', function(InvoiceLine $invoiceLine) {
                return $invoiceLine->PriceTotalFormatted;
            })
            ->addColumn('PRICETOTAL_INCVAT_FORMATTED', function(InvoiceLine $invoiceLine) {
                return $invoiceLine->PriceTotalIncVatFormatted;
            })
            ->addColumn('PERIOD_FORMATTED', function(InvoiceLine $invoiceLine) {
                return $invoiceLine->PeriodFormatted;
            })
            ->addColumn('VAT', function(InvoiceLine $invoiceLine) {
                return ($invoiceLine->vat->PercentageFormatted ?? '');
            })
            ->addColumn('LEDGER', function(InvoiceLine $invoiceLine) {
                return isset($invoiceLine->ledger) ? $invoiceLine->ledger->ACCOUNT . ' - ' . $invoiceLine->ledger->DESCRIPTION : '';
            });
    }

    public function allByInvoiceDatatable(Request $request, int $ID)
    {
        $this->whereClause = [];
        $this->datatableFilter = [];

        if ($ID > 0) {
            $this->whereClause = array(
                ['FK_FINANCE_INVOICE', $ID],
            );
        }

        return parent::allDatatable($request);
    }

    protected function beforeDetail(int $ID, $item)
    {
        $none = ['' => KJLocalization::translate('Algemeen', 'Niets geselecteerd', 'Niets geselecteerd') . '..'];

        $ledgersOri = Ledger::select(DB::raw("CONCAT(ACCOUNT,' - ',DESCRIPTION) AS COMBINEDDESCRIPTION"),'ID','ACTIVE')->where('ACTIVE', TRUE)->orderBy('COMBINEDDESCRIPTION')->pluck('COMBINEDDESCRIPTION', 'ID');
        $ledgers = $none + $ledgersOri->toArray();

        $vatOri = VAT::where('ACTIVE', true)->orderBy('DESCRIPTION')->pluck('DESCRIPTION', 'ID');
        $vat = $none + $vatOri->toArray();

        $bindings = array(
            ['ledgers', $ledgers],
            ['vat', $vat]
        );

        return $bindings;
    }

    public function delete(int $ID)
    {
        $item = $this->find($ID);

        if ($item) {
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => KJLocalization::translate('Algemeen', 'Item verwijderd', 'Het item is verwijderd'),
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => KJLocalization::translate('Algemeen', 'Geen item gevonden', 'Er is geen item gevonden'),
            ], 200);
        }
    }
}