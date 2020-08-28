<?php

namespace App\Libraries\Admin;

use App\Mail\Admin\Invoice\NewInvoice;
use App\Mail\Admin\Invoice\NewInvoiceCompensation;
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

    public static function generateDocuments(int $ID, string $state)
    {
        $invoice = Invoice::find($ID);

        $anonymize_state = 'anonymize';
        if ($state === 'final') {
            $anonymize_state = 'anonymize_final';
        }

        // Generate invoice
        $invoice_report = self::generateReport($invoice, $state);

        // Generate extra documents when compensated
        if($invoice->project) {
            if ($invoice->project->COMPENSATED) {
                // Generate compensation letter
                if ($invoice->label->proxy) {
                    $compensation_report = CompensationUtils::generateReport($invoice, $state);
                }

                // Generate anonymized invoice
                $invoice_anonymized_report = self::generateReport($invoice, $anonymize_state);
            }
        }

        return [
            'invoice_report' => $invoice_report,
            'compensation_report' => ($compensation_report ?? null),
            'invoice_anonymized_report' => ($invoice_anonymized_report ?? null),
        ];
    }

    public static function generateReport($invoice, string $state)
    {
        $invoice->relation->generateDebtornumber();

        // Factuurnummer aanmaken
        if ($state === 'final') {
            $invoice->generateNumber();
            $invoice->refresh();
        }

        $pdfPaper = $invoice->label->document;

        $locale = App::getLocale();
        $localeId = config('language.langs')[array_search(strtoupper($locale), array_column(config('language.langs'), 'CODE'))]['ID'];

        $paymentText = KJLocalization::translate('Admin - Facturen', 'Betalingstekst', 'Wij verzoeken u deze factuur voor :EXPIRATION_DATE te voldoen op bankrekening :IBAN_NUMBER ten name van Exsist B.V. te Doetinchem, onder vermelding van het factuurnummer en uw debiteurnummer (:INVOICE_NUMBER, :NUMBER_DEBTOR)', [
            'EXPIRATION_DATE' => $invoice->getExpirationDateFormattedAttribute() ?? 'Vervaldatum (concept)',
            'IBAN_NUMBER' => $invoice->label->IBAN_NUMBER,
            'INVOICE_NUMBER' => $invoice->NUMBER ?? KJLocalization::translate('Admin - Facturen', 'Concept', 'Concept', [], $locale),
            'NUMBER_DEBTOR' => $invoice->relation->NUMBER_DEBTOR
        ], $locale);

        $title = KJLocalization::translate('Admin - Facturen', 'Factuur', 'Factuur', [], $locale);
        if ($invoice->IS_ADVANCE) {
            $title = KJLocalization::translate('Admin - Facturen', 'Voorschotfactuur', 'Voorschotfactuur', [], $locale);
        }

        $is_anonymized = 0;
        if($invoice->project){
            if ($invoice->project->COMPENSATED && in_array($state, ['anonymize', 'anonymize_final'])) {
                $is_anonymized = 1;
            }
        }
        $reportName = KJLocalization::translate('Admin - Facturen', 'Factuur', 'Factuur', [], $locale) . ' ' . ($invoice->NUMBER ?? KJLocalization::translate('Admin - Facturen', 'Concept', 'Concept', [], $locale));
        if($is_anonymized === 1){
            $reportName = KJLocalization::translate('Admin - Facturen', 'Geanonimiseerde kopie factuur', 'Geanonimiseerde kopie factuur', [], $locale) . ' ' . ($invoice->NUMBER ?? KJLocalization::translate('Admin - Facturen', 'Concept', 'Concept', [], $locale));
        }

        $data = array(
            'OutputFolder' => config('documentservice.output_folder') . '\\' . $invoice->getTable() . '\\' . $invoice->ID . '\\Facturen',
            'Sjabloon' => 'Invoice',
            'ReportName' => $reportName,
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
                    'Query' => "SELECT CA.* , '" . KJLocalization::translate('Admin - Facturen', 'Nederland', 'Nederland', [], $locale) . "' AS COUNTRY FROM dbo.CRM_RELATION_ADDRESS CRA WITH (NOLOCK) JOIN CORE_ADDRESS CA WITH (NOLOCK) ON CA.ID = CRA.FK_CORE_ADDRESS WHERE CRA.ID = {1} AND CRA.ACTIVE = 1",
                    'Parameters' => [(int)$invoice->FK_CRM_RELATION_ADDRESS]
                ),
                array(
                    'Identifier' => "FINANCE_INVOICE",
                    'Query' => "SELECT FI.ID, ISNULL(IS_CREDIT, 0) AS IS_CREDIT, 
                    ISNULL(HAS_SPECIFICATION, 0) AS HAS_SPECIFICATION, 
                    CASE WHEN FI.NUMBER IS NULL THEN '".KJLocalization::translate('Admin - Facturen', 'Concept', 'Concept', [], $locale)."' ELSE CAST(FI.NUMBER AS VARCHAR(100)) END AS NUMBER, 
                    FI.DATE, 
                    FI.EXPIRATION_DATE, 
                    ISNULL(FI.PRICE_TOTAL_EXCL,0) AS TOTALEXCL, 
                    ISNULL(FI.PRICE_TOTAL_INCL,0) AS TOTALINCL, 
                    ISNULL(FI.VAT_TOTAL,0) AS TOTALVAT, 
                    '".$paymentText."' AS PAYMENTTEXT,
                    CC.FULLNAME AS EMPLOYEE,
					P.START_DATE AS FIRST_SICKDAY,
					P.POLICY_NUMBER AS POLICY_NUMBER
                    FROM FINANCE_INVOICE FI WITH(NOLOCK) 
					LEFT JOIN dbo.PROJECT P ON P.ID = FI.FK_PROJECT
					LEFT JOIN dbo.CRM_CONTACT CC ON CC.ID = P.FK_CRM_CONTACT_EMPLOYEE
					 WHERE FI.ID = {1}",
                    'Parameters' => [$invoice->ID]
                ),
                array(
                    'Identifier' => "FINANCE_INVOICE_ITEM",
                    'Query' => "EXEC [REPORT_INVOICE_LINE] @FK_FINANCE_INVOICE = {1}",
                    'Parameters' => [$invoice->ID]
                ),
                array(
                    'Identifier' => "FINANCE_INVOICE_ITEM_SPECIFICATION",
                    'Query' => "EXEC [REPORT_INVOICE_LINE_SPECIFICATION] @FK_FINANCE_INVOICE = {1}",
                    'Parameters' => [$invoice->ID]
                ),
                array(
                    'Identifier' => "FINANCE_INVOICE_VAT",
                    'Query' => "EXEC [REPORT_INVOICE_VAT] @FK_FINANCE_INVOICE = {1}",
                    'Parameters' => [$invoice->ID]
                ),
                array(
                    'Identifier' => "TEXT",
                    'Query' => "SELECT " .
                        " '" . $title . "' AS TITLE, " .
                        " CAST(".$is_anonymized." AS BIT) AS IS_ANONYMIZED, " .
                        " '" . KJLocalization::translate('Admin - Facturen', 'Stukprijs', 'Stukprijs', [], $locale) . "' AS PRIJS, " .
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
                        " '" . KJLocalization::translate('Admin - Facturen', 'T.b.v. werknemer', 'T.b.v. werknemer', [], $locale) . "' AS TBV_WERKNEMER, " .
                        " '" . KJLocalization::translate('Admin - Facturen', 'Polisnummer', 'Polisnummer', [], $locale) . "' AS POLISNUMMER, " .
                        " '" . KJLocalization::translate('Admin - Facturen', 'Eerste ziektedag', 'Eerste ziektedag', [], $locale) . "' AS EERSTE_ZIEKTEDAG, " .
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
            // Move file from storage ftp to private ft
            if (config('filesystems.disks.ftp.host') != config('filesystems.disks.ftp_docservice.host')) {
                Storage::disk('ftp')->put($resultArray['filename'], Storage::disk('ftp_docservice')->get($resultArray['filename']));

                // Delete file from storage ftp
                Storage::disk('ftp_docservice')->delete($resultArray['filename']);
            }

            // Copy file to project document folder if final invoice
            if ($invoice->project && $invoice->NUMBER) {


                $pieces = explode("//", $resultArray['filename']);
                $pieces[0] = $invoice->project->getTable();
                $pieces[1] = $invoice->project->ID;
                $filename = implode("//",$pieces);



                Storage::disk('ftp')->put($filename, Storage::disk('ftp')->get($resultArray['filename']));

                // Get file info
                $documentInfo = pathinfo($filename);

                $fileSize = Storage::disk('ftp')->size(str_replace('//', '\\', $filename));
                // Add document to project
                $document = new Document([
                    'FK_TABLE' => $invoice->project->getTable(),
                    'FK_ITEM' => $invoice->project->ID,
                    'UPLOADER_FK_TABLE' => Auth::guard()->user()->getTable(),
                    'UPLOADER_FK_ITEM' => Auth::guard()->user()->ID,
                    'FILEPATH' => str_replace('//', '\\', $filename),
                    'FILESIZE' => $fileSize,
                    'TITLE' => $documentInfo['filename'],
                    'FILETYPE' => ($documentInfo['extension'] ?? 'file')
                ]);
                $document->save();
            }

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

            if ($state === 'anonymize_final') {
                $invoice->FK_DOCUMENT_ANONYMIZED = $document->ID;
                $invoice->TS_GENERATE_ANONYMIZED = new DateTime();
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
            self::generateDocuments($ID, 'final');
            $invoice->refresh();
        }

//       als het factuur document bestaat, maar niet op de ftp
        if (($invoice->document) && !Storage::disk('ftp')->exists($invoice->document->FILEPATH)) {
            $invoice->update(['FK_DOCUMENT' => null]);
            $invoice->document->delete();
            self::generateReport($invoice, 'final');
        }
//      als het geanonimiseerde document bestaat, maar niet op de ftp
        if (($invoice->document_anonymized) && !Storage::disk('ftp')->exists($invoice->document_anonymized->FILEPATH)) {
            $invoice->update(['FK_DOCUMENT_ANONYMIZED' => null]);
            $invoice->document_anonymized->delete();
            self::generateReport($invoice, 'anonymize_final');
        }
//      als de vergoedingsbrief bestaat, maar niet op de ftp
        if (($invoice->document_compensation_letter) && !Storage::disk('ftp')->exists($invoice->document_compensation_letter->FILEPATH)) {
            $invoice->update(['FK_DOCUMENT_COMPENSATION_LETTER' => null]);
            CompensationUtils::generateReport($invoice, 'anonymize_final');
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

            // Send to proxy relation
            if($invoice->project) {
                if ($invoice->project->COMPENSATED) {
                    if ($invoice->label->proxy) {
                        if ($invoice->label->proxy->EMAILADDRESS && ($invoice->label->proxy->EMAILADDRESS != '')) {
                            try {
                                Mail::to($invoice->label->proxy->EMAILADDRESS)->send(new NewInvoiceCompensation($invoice));
                            } catch (\Exception $exception) {
                                return response()->json([
                                    'success' => false,
                                    'message' => KJLocalization::translate('Admin - Facturen', 'Versturen vergoeding mislukt', 'Het is niet gelukt om de vergoeding te versturen. Probeer het opnieuw.') . PHP_EOL . $exception->getMessage()
                                ], 200);
                            }
                        }
                    }
                }
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
            return [
                'success' => true,
                'print' => !$digital,
                'url' => URL::to('/') . '/api/',
                'fileRequest' => $fileRequest,
            ];
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
            self::generateDocuments($ID, 'final');
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