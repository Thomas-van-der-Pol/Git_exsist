<?php

namespace App\Http\Controllers\Admin\Finance\Exact\Models;

use App\Http\Controllers\Admin\Finance\Exact\ExactBaseResource;
use App\Models\Admin\Finance\Invoice;
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
        $journal = 70;

        $receivables = new \Picqer\Financials\Exact\ReceivablesList($this->connection);
        $receivables = $receivables->filter("JournalCode eq '$journal'");

        $entryNumbers = array_column($receivables, 'EntryNumber');
        $chunks = array_chunk($entryNumbers, 1000);

        // Alles wat niet in de lijst receivables terugkomt, mag op volledig betaald komen te staan
        try
        {
            // Update paid in chunks of 1000 ids
            foreach ($chunks as $chunk) {
                Invoice::whereNotNull('EXACT_INVOICE_ID')
                    ->whereNotIn('NUMBER', $chunk)
                    ->update(['PAID' => true]);
            }

            // Update each invoice which is still receivable
            foreach ($receivables as $receivable)
            {
                $invoice = Invoice::where('NUMBER', (string)$receivable->EntryNumber)->first();
                if ($invoice !== null) {
                    $invoice->PAID = false;
                    $invoice->PAID_DATE = null;
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