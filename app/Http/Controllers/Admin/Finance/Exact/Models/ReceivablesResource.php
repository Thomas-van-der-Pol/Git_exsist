<?php

namespace App\Http\Controllers\Admin\Finance\Exact\Models;

use App\Http\Controllers\Admin\Finance\Exact\ExactBaseResource;
use App\Models\Finance\Invoice;
use Illuminate\Support\Facades\DB;
use KJLocalization;

class ReceivablesResource extends ExactBaseResource
{
    public function __construct()
    {
        parent::__construct();

        $this->doConnect();
    }

    public function import()
    {
        $receivables = new \Picqer\Financials\Exact\ReceivableList($this->connection);
        $receivables = $receivables->get();

        $entryNumbers = array_column($receivables, 'EntryNumber');
        $entryNumberIDs = implode(",", $entryNumbers);

        // Alles wat niet in de lijst receivables terugkomt, mag op volledig betaald komen te staan
        try
        {
            DB::insert("EXEC [FINANCE_ACCOUNTANCY_UPDATE_RECEIVABLES] '".$entryNumberIDs."'");

            foreach ($receivables as $receivable)
            {
                $invoice = Invoice::where('NUMBER', (string)$receivable->EntryNumber)->first();
                if ($invoice !== null) {
                    $invoice->PAID = false;
                    $invoice->ALREADY_PAID = ($invoice->SALEPRICEINCVAT - $receivable->Amount);
                    $invoice->save();
                }
            }

            return response()->json([
                'success' => true
            ], 200);
        }
        catch (\Exception $e)
        {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 200);
        }
    }
}