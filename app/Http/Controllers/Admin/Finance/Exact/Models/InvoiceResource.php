<?php

namespace App\Http\Controllers\Admin\Finance\Exact\Models;

use App\Http\Controllers\Admin\Finance\Exact\ExactBaseResource;
use App\Models\Admin\Finance\Invoice;
use KJ\Localization\libraries\LanguageUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use KJLocalization;

class InvoiceResource extends ExactBaseResource
{

    protected $model = 'App\Models\Admin\Finance\Invoice';

    protected $detailViewName = 'admin.finance.exact.models.invoice.detail';

    public function __construct()
    {
        parent::__construct();

        $this->doConnect();
    }

    protected function doOrderBy(&$items, $field, $sort)
    {
        // Tabelnaam eraf halen, want die hebben we niet nodig in een SP
        $field = str_replace($this->baseTable.'.', '', $field);

        if (strtolower($sort) == 'asc') {
            $items = $items->sortBy($field);
        } else {
            $items = $items->sortByDesc($field);
        }
    }

    protected function getSortField($sortField)
    {
        if ($sortField === 'DATE_FORMATTED') {
            return 'DATE';
        } else if ($sortField === 'PRICE_TOTAL_EXCL_FORMATTED') {
            return 'PRICE_TOTAL_EXCL';
        } else if ($sortField === 'PRICE_TOTAL_INCL_FORMATTED') {
            return 'PRICE_TOTAL_INCL';
        } else {
            return parent::getSortField($sortField);
        }
    }

    protected function allInternal(Request $request, bool $doDatatableFilter = false, array &$pagination = [], array &$sort = [])
    {
        $query = $request->query($this->datatableQueryKey);

        $filter = null;
        if ($doDatatableFilter === true) {
            $filter = isset($query['query']['ADM_ACCOUNTANCY_FILTER_SEARCH']) ? $query['query']['ADM_ACCOUNTANCY_FILTER_SEARCH'] : null;
        }

        $items = collect(DB::select('EXEC [FINANCE_ACCOUNTANCY_INVOICE] ?', [
            $filter
        ]));

        //Sortering
        $this->applySorting($items, $sort);

        // Pagination
        $this->applyPagination($items, $pagination);
        if ((int)$pagination['perpage'] !== -1) {
            $items = $items->slice((($pagination['page'] - 1) * $pagination['perpage']))->take($pagination['perpage']);
        } else {
            $items = $items->slice(0)->take(999999);
        }

        return $items;
    }

    protected function beforeDatatable($datatable)
    {
        $datatable->addColumn('DATE_FORMATTED', function($item) {
            return isset($item->DATE) ? date(LanguageUtils::getDateFormat(), strtotime($item->DATE)) : '';
        })->addColumn('PRICE_TOTAL_EXCL_FORMATTED', function($item) {
            return isset($item->PRICE_TOTAL_EXCL) ? '€ ' . number_format($item->PRICE_TOTAL_EXCL, 2, LanguageUtils::getDecimalPoint(), LanguageUtils::getThousandsSeparator()) : '';
        })->addColumn('PRICE_TOTAL_INCL_FORMATTED', function($item) {
            return isset($item->PRICE_TOTAL_INCL) ? '€ ' . number_format($item->PRICE_TOTAL_INCL, 2, LanguageUtils::getDecimalPoint(), LanguageUtils::getThousandsSeparator()) : '';
        });
    }

    protected function beforeDetail(int $ID, $item)
    {
        $invoiceItems = collect(DB::select('EXEC [FINANCE_ACCOUNTANCY_INVOICE_ITEM] ?', [
            $ID
        ]));

        $bindings = array(
            ['invoiceItems', $invoiceItems]
        );

        return $bindings;
    }

    public function export(Request $request)
    {
        // Want we moeten de juiste tijden hebben
        date_default_timezone_set('Europe/Amsterdam');

        $ids = json_decode($request->get('ids'), true);
        $idString = implode(",", $ids);

        $items = collect(DB::select('EXEC [FINANCE_ACCOUNTANCY_INVOICE] ?, ?', [
            '',
            $idString
        ]));

        $accounts = new \Picqer\Financials\Exact\Account($this->connection);
        $glAccounts = new \Picqer\Financials\Exact\GLAccount($this->connection);

        foreach ($items as $item) {
            $invoice = Invoice::find($item->ID);
            $relation = $invoice->relation;
            $account = null;

            $exactId = $relation->EXACT_DEBTOR_ID ? $relation->EXACT_DEBTOR_ID : $accounts->findID("$item->DEBTORNUMBER");
            if($exactId <> '') {
                // Bestaand account
                $account = $accounts->find("{" . $exactId . "}");
            }

            if (isset($account))
            {
                try
                {
                    $salesEntry = new \Picqer\Financials\Exact\SalesEntry($this->connection);

                    // Versturen naar Exact
                    $salesEntry->Customer = $account->ID;
                    $salesEntry->Type = $item->TYPE;
                    $salesEntry->Journal = 70;
                    $salesEntry->EntryNumber = $item->NUMBER;
                    $salesEntry->ReportingYear = $item->FINANCIAL_YEAR;
                    $salesEntry->ReportingPeriod = $item->FINANCIAL_PERIOD;
                    $salesEntry->Date = $item->DATE;
                    $salesEntry->PaymentCondition = $item->PAYMENT_CONDITION;
                    $salesEntry->Description = $item->DESCRIPTION;
                    $salesEntry->YourRef = $item->REFERENCE;

                    // Lines toevoegen
                    $invoiceItems = collect(DB::select('EXEC [FINANCE_ACCOUNTANCY_INVOICE_ITEM] ?', [
                        $item->ID
                    ]));

                    $salesEntryLines = [];

                    foreach ($invoiceItems as $invoiceItem)
                    {
                        $ledger = collect($glAccounts->filter("Code eq '$invoiceItem->GL_ACCOUNT_CODE'"))->first();
                        if (!$ledger) {
                            throw new \Exception('Grootboekrekening ' . $invoiceItem->GL_ACCOUNT_CODE . ' niet gevonden');
                        }

                        $salesEntryLine = new \Picqer\Financials\Exact\SalesEntryLine($this->connection);
                        $salesEntryLine->Type = 20;
                        $salesEntryLine->Description = $invoiceItem->DESCRIPTION;
                        $salesEntryLine->GLAccount = $ledger->ID;
                        $salesEntryLine->AmountFC = $invoiceItem->AMOUNT_DC; // Bedrag
                        $salesEntryLine->VATAmountFC = $invoiceItem->AMOUNT_VAT_DC; // BTW bedrag
                        $salesEntryLine->VATBaseAmountDC = $invoiceItem->AMOUNT_VAT_BASE_DC;
                        $salesEntryLine->VATCode = $invoiceItem->VATCODE;

                        array_push($salesEntryLines, $salesEntryLine);
                    }

                    $salesEntry->SalesEntryLines = collect($salesEntryLines);
                    $salesEntry->save();

                    // Relatie bijwerken
                    $invoice->EXACT_INVOICE_ID = $salesEntry->EntryID;
                    $invoice->EXACT_INVOICE_ERROR = '';
                    $invoice->EXACT_INVOICE_LASTSYNC = date('Y-m-d H:i:s');
                    $invoice->save();
                }
                catch (\Exception $e)
                {
                    $invoice->EXACT_INVOICE_ERROR = $e->getMessage();
                    $invoice->save();
                }
            }
            else
            {
                $invoice->EXACT_INVOICE_ERROR = 'Debiteur niet in Exact';
                $invoice->save();
            }
        }

        return response()->json([
            'success' => true
        ], 200);
    }
}