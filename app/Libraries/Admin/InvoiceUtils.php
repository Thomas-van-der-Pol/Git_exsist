<?php

namespace App\Libraries\Admin;

use App\Mail\Admin\Invoice\NewInvoice;
use App\Mail\Admin\Invoice\Reminder;
use App\Models\Admin\Finance\Invoice;
use App\Models\Core\Document;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use KJ\Core\libraries\ReportUtils;
use DateTime;
use KJ\Core\models\FileRequest;
use KJLocalization;

class InvoiceUtils
{

    public static function generateReport(int $ID, string $state)
    {
        $invoice = Invoice::find($ID);
        $invoice->relation->generateDebtornumber();

        // Factuurnummer aanmaken
        if ($state === 'final') {
            $invoice->generateNumber();
            $invoice->refresh();
        }

        $pdfPaper = $invoice->label->document;

        $locale = App::getLocale();
        $localeId = config('language.langs')[array_search(strtoupper($locale), array_column(config('language.langs'), 'CODE'))]['ID'];

        $paymentText = KJLocalization::translate('Admin - Facturen', 'Betalingstekst', 'Gaarne betalingen binnen :DAYS dagen op IBAN.: :IBAN_NUMBER, BIC: :BIC_NUMBER, Btw nr: :VAT_NUMBER', [
            'DAYS' => $invoice->getPaymentCondition(),
            'IBAN_NUMBER' => $invoice->label->IBAN_NUMBER,
            'BIC_NUMBER' => $invoice->label->BIC_NUMBER,
            'VAT_NUMBER' => $invoice->label->VAT_NUMBER
        ], $locale);
        $title = KJLocalization::translate('Admin - Facturen', 'Factuur', 'Factuur', [], $locale);
        if ($invoice->IS_ADVANCE) {
            $title = KJLocalization::translate('Admin - Facturen', 'Voorschotfactuur', 'Voorschotfactuur', [], $locale);
        }

        $data = array(
            'OutputFolder' => config('documentservice.output_folder') . '\\' . $invoice->getTable() . '\\' . $ID,
            'Sjabloon' => 'Invoice',
            'ReportName' => KJLocalization::translate('Admin - Facturen', 'Factuur', 'Factuur', [], $locale) . ' ' . ($invoice->NUMBER ?? KJLocalization::translate('Admin - Facturen', 'Concept', 'Concept', [], $locale)),
            'Statements' => array(
                array(
                    'Identifier' => "CRM_RELATION",
                    'Query' => "SELECT CR.ID, ISNULL(NUMBER_DEBTOR,'') AS NUMBER_DEBTOR, CR.NAME, CR.VAT_LIABLE, CR.VAT_NUMBER FROM CRM_RELATION CR WITH(NOLOCK) WHERE CR.ID = {1}",
                    'Parameters' => [(int)$invoice->FK_CRM_RELATION]
                ),
                array(
                    'Identifier' => "CRM_RELATION_CONTACT",
                    'Query' => "SELECT [dbo].[CRM_CONTACT_ATTN_NAME]({1}, {2}) AS FULLNAME",
                    'Parameters' => [(int)$invoice->FK_CRM_CONTACT, $localeId]
                ),
                array(
                    'Identifier' => "CRM_RELATION_ADDRESS",
                    'Query' => "SELECT CA.* FROM dbo.CRM_RELATION_ADDRESS CRA WITH (NOLOCK) JOIN CORE_ADDRESS CA WITH (NOLOCK) ON CA.ID = CRA.FK_CORE_ADDRESS WHERE CRA.ID = {1} AND CRA.ACTIVE = 1",
                    'Parameters' => [(int)$invoice->FK_CRM_RELATION_ADDRESS]
                ),
                array(
                    'Identifier' => "FINANCE_INVOICE",
                    'Query' => "SELECT FI.ID, ISNULL(IS_CREDIT, 0) AS IS_CREDIT, ISNULL(HAS_SPECIFICATION, 0) AS HAS_SPECIFICATION, CASE WHEN FI.NUMBER IS NULL THEN '".KJLocalization::translate('Admin - Facturen', 'Concept', 'Concept', [], $locale)."' ELSE CAST(FI.NUMBER AS VARCHAR(100)) END AS NUMBER, FI.DATE, FI.EXPIRATION_DATE, ISNULL(FI.PRICE_TOTAL_EXCL,0) AS TOTALEXCL, ISNULL(FI.PRICE_TOTAL_INCL,0) AS TOTALINCL, ISNULL(FI.VAT_TOTAL,0) AS TOTALVAT, '".$paymentText."' AS PAYMENTTEXT FROM FINANCE_INVOICE FI WITH(NOLOCK) WHERE FI.ID = {1}",
                    'Parameters' => [$ID]
                ),
                array(
                    'Identifier' => "FINANCE_INVOICE_ITEM",
                    'Query' => "EXEC [REPORT_INVOICE_LINE] @FK_FINANCE_INVOICE = {1}",
                    'Parameters' => [$ID]
                ),
                array(
                    'Identifier' => "FINANCE_INVOICE_ITEM_SPECIFICATION",
                    'Query' => "EXEC [REPORT_INVOICE_LINE_SPECIFICATION] @FK_FINANCE_INVOICE = {1}",
                    'Parameters' => [$ID]
                ),
                array(
                    'Identifier' => "FINANCE_INVOICE_VAT",
                    'Query' => "EXEC [REPORT_INVOICE_VAT] @FK_FINANCE_INVOICE = {1}",
                    'Parameters' => [$ID]
                ),
                array(
                    'Identifier' => "TEXT",
                    'Query' => "SELECT " .
                        " '" . $title . "' AS TITLE, " .
                        " '" . KJLocalization::translate('Admin - Facturen', 'Prijs', 'Prijs', [], $locale) . "' AS PRIJS, " .
                        " '" . KJLocalization::translate('Admin - Facturen', 'Aantal', 'Aantal', [], $locale) . "' AS AANTAL, " .
                        " '" . KJLocalization::translate('Admin - Facturen', 'Totaal', 'Totaal', [], $locale) . "' AS TOTAAL, " .
                        " '" . KJLocalization::translate('Admin - Facturen', 'Omschrijving', 'Omschrijving', [], $locale) . "' AS OMSCHRIJVING, " .
                        " '" . KJLocalization::translate('Admin - Facturen', 'Creditfactuur', 'Creditfactuur', [], $locale) . "' AS CREDITFACTUUR, " .
                        " '" . KJLocalization::translate('Admin - Facturen', 'Factuur', 'Factuur', [], $locale) . "' AS FACTUUR, " .
                        " '" . KJLocalization::translate('Admin - Facturen', 'Factuur specificatie', 'Factuur specificatie', [], $locale) . "' AS FACTUUR_SPECIFICATIE, " .
                        " '" . KJLocalization::translate('Admin - Facturen', 'Tav', 'T.a.v.', [], $locale) . "' AS TAV, " .
                        " '" . KJLocalization::translate('Admin - Facturen', 'Debiteurnummer', 'Debiteurnummer', [], $locale) . "' AS DEBITEURNUMMER, " .
                        " '" . KJLocalization::translate('Admin - Facturen', 'Factuurnummer', 'Factuurnummer', [], $locale) . "' AS FACTUURNUMMER, " .
                        " '" . KJLocalization::translate('Admin - Facturen', 'Factuurdatum', 'Factuurdatum', [], $locale) . "' AS FACTUURDATUM, " .
                        " '" . KJLocalization::translate('Admin - Facturen', 'Vervaldatum', 'Vervaldatum', [], $locale) . "' AS VERVALDATUM, " .
                        " '" . KJLocalization::translate('Admin - Facturen', 'Totaal inclusief btw', 'Totaal inclusief btw', [], $locale) . "' AS TOTAL_INCL, " .
                        " '" . KJLocalization::translate('Admin - Facturen', 'Totaal exclusief btw', 'Totaal exclusief btw', [], $locale) . "' AS TOTAL_EXCL, " .
                        " '" . KJLocalization::translate('Admin - Facturen', 'Btw bedrag', 'Btw bedrag', [], $locale) . "' AS TOTAL_VAT, " .
                        " '" . KJLocalization::translate('Admin - Facturen', 'Blz.', 'Blz.', [], $locale) . "' AS BLZ, " .
                        " '" . KJLocalization::translate('Admin - Facturen', 'Btw', 'Btw', [], $locale) . "' AS BTW, ".
                        " '" . KJLocalization::translate('Admin - Facturen', 'Uw btw nummer', 'Uw btw nummer', [], $locale) . "' AS BTW_NUMMER, ".
                        " '" . KJLocalization::translate('Admin - Facturen', 'over', 'over', [], $locale) . "' AS OVERBTW ",
                    'Parameters' => []
                ),
            ),
            'ExportAs' => 'PDF'
        );

        if ($pdfPaper) {
            $data = array_merge($data, [
                'PDFPaperFirst' => config('documentservice.output_folder') . '\\' . str_replace('/', '\\', $pdfPaper->FILEPATH),
                'PDFPaperContinuation' => config('documentservice.output_folder') . '\\' . str_replace('/', '\\', $pdfPaper->FILEPATH)
            ]);
        }

        // Report ophalen
        $result = ReportUtils::reportDocument($data);
        $resultArray = is_array($result) ? $result : json_decode($result->getContent(), true);

        if ($resultArray['statusCode'] == 200) {
            // Get file info
            $documentInfo = pathinfo($resultArray['filename']);

            $fileSize = Storage::disk('ftp')->size(str_replace('//', '\\', $resultArray['filename']));

            // Add document to invoice
            $document = new Document([
                'FK_TABLE' => $invoice->getTable(),
                'FK_ITEM' => $invoice->ID,
                'UPLOADER_FK_TABLE' => Auth::guard()->user()->getTable(),
                'UPLOADER_FK_ITEM' => Auth::guard()->user()->ID,
                'FILEPATH' => str_replace('//', '\\', $resultArray['filename']),
                'FILESIZE' => $fileSize,
                'TITLE' => $documentInfo['filename'],
                'FILETYPE' => ($documentInfo['extension'] ?? 'file')
            ]);
            $document->save();

            // Link document on invoice
            if ($state === 'final') {
                $invoice->FK_DOCUMENT = $document->ID;
                $invoice->TS_GENERATE = new DateTime();
                $invoice->FK_CORE_WORKFLOWSTATE = config('workflowstate.INVOICE_FINAL');
                $invoice->save();
            }

            return [
                'success' => true,
                'file' => $resultArray,
                'document' => $document
            ];
        } else {
            return [
                'success' => false,
                'file' => $resultArray
            ];
        }
    }

    public static function sendInvoice(int $ID, $isBulk = false)
    {
        $invoice = Invoice::find($ID);
        $digital = (($invoice->relation->INVOICE_ELECTRONIC ?? false) == true);

        if (($digital == true) && (!isset($invoice->contact->EMAILADDRESS))) {
            if(!$isBulk){
                return response()->json([
                    'success' => false,
                    'message' => KJLocalization::translate('Admin - Facturen', 'Geen mailadres', 'Geen emailadres ingesteld bij de contactpersoon van de factuur. Factuur niet verstuurd')
                ], 200);
            }
            else return false;
        }

        // Indien nog geen factuur gemaakt
        if (!$invoice->document) {
            $report = self::generateReport($ID, 'final');
            $invoice->refresh();
        }

        // Indien factuur aanwezig is
        $fileRequest = null;
        if ($invoice->document) {
            // Status bijwerken
            if ($invoice->FK_CORE_WORKFLOWSTATE != config('workflowstate.INVOICE_FINAL')) {
                $invoice->FK_CORE_WORKFLOWSTATE = config('workflowstate.INVOICE_FINAL');
                $invoice->save();
            }

            if ($digital == true) {
                try {
                    Mail::to($invoice->contact->EMAILADDRESS)->send(new NewInvoice($invoice));
                }
                catch(\Exception $exception){
                    return response()->json([
                        'success' => false,
                        'message' => KJLocalization::translate('Admin - Facturen', 'Versturen factuur mislukt', 'Het is niet gelukt om de factuur te genereren en te versturen. Probeer het opnieuw.').PHP_EOL.$exception->getMessage()
                    ], 200);
                }
            } else {
                // Create file request
                $fileRequest = new FileRequest([
                    'OBJECT' => $invoice->document->getTable(),
                    'OBJECT_ID' => $invoice->document->ID,
                    'REQUEST_OBJECT' => Auth::guard('admin')->user()->getTable(),
                    'REQUEST_OBJECT_ID' => Auth::guard('admin')->user()->ID,
                    'FILENAME' => $invoice->document->FILEPATH
                ]);
                $fileRequest->save();
                $fileRequest->refresh();
            }
        }

        // Status bijwerken
        if ($invoice->FK_CORE_WORKFLOWSTATE != config('workflowstate.INVOICE_FINAL')) {
            $invoice->FK_CORE_WORKFLOWSTATE = config('workflowstate.INVOICE_FINAL');
            $invoice->save();
        }

        $invoice->addRemark(KJLocalization::translate('Admin - Facturen', 'Factuur verstuurd', 'Factuur verstuurd'));

        if(!$isBulk){
            return response()->json([
                'success' => true,
                'print' => !$digital,
                'url' => URL::to('/') . '/api/',
                'fileRequest' => $fileRequest,
                'message' => KJLocalization::translate('Admin - Facturen', 'Factuur verstuurd', 'Factuur verstuurd')
            ], 200);
        }
        else {
            return true;
        }
    }

    public static function sendInvoiceReminder(int $ID)
    {
        $invoice = Invoice::find($ID);
        $digital = (($invoice->relation->INVOICE_ELECTRONIC ?? false) == true);

        if ($invoice->FK_CORE_WORKFLOWSTATE != config('workflowstate.INVOICE_FINAL')) {
             return response()->json([
                 'success' => false,
                 'message' => KJLocalization::translate('Admin - Facturen', 'Factuur is nog niet definitief', 'Deze factuur is nog niet definitief. Herinnering sturen niet mogelijk!')
             ], 200);
        }

        if (($digital == true) && (!isset($invoice->contact->EMAILADDRESS))) {
            return response()->json([
                'success' => false,
                'message' => KJLocalization::translate('Admin - Facturen', 'Geen mailadres', 'Geen emailadres ingesteld bij de contactpersoon van de factuur. Factuur niet verstuurd')
            ], 200);
        }

        // Indien nog geen factuur gemaakt
        if (!$invoice->document) {
            $report = self::generateReport($ID, 'final');
            $invoice->refresh();
        }

        // Indien factuur aanwezig is
        $fileRequest = null;
        if ($invoice->document) {

            if ($digital == true) {
                try {
                    Mail::to($invoice->contact->EMAILADDRESS)->send(new Reminder($invoice));
                }
                catch(\Exception $exception){
                    return response()->json([
                        'success' => false,
                        'message' => KJLocalization::translate('Admin - Facturen', 'Versturen herinnering mislukt', 'Het is niet gelukt om de herinnering te genereren en te versturen. Probeer het opnieuw.')
                    ], 200);
                }
            } else {
                // Create file request
                $fileRequest = new FileRequest([
                    'OBJECT' => $invoice->document->getTable(),
                    'OBJECT_ID' => $invoice->document->ID,
                    'REQUEST_OBJECT' => Auth::guard('admin')->user()->getTable(),
                    'REQUEST_OBJECT_ID' => Auth::guard('admin')->user()->ID,
                    'FILENAME' => $invoice->document->FILEPATH
                ]);
                $fileRequest->save();
                $fileRequest->refresh();
            }
        }

        $invoice->addRemark(KJLocalization::translate('Admin - Facturen', 'Herinnering verstuurd', 'Herinnering verstuurd'));

        return response()->json([
            'success' => true,
            'print' => !$digital,
            'url' => URL::to('/') . '/api/',
            'fileRequest' => $fileRequest,
            'message' => KJLocalization::translate('Admin - Facturen', 'Herinnering verstuurd!', 'Herinnering verstuurd!')
        ], 200);
    }
}