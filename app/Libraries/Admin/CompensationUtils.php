<?php

namespace App\Libraries\Admin;

use App\Models\Core\Document;
use DateTime;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use KJ\Core\libraries\ReportUtils;
use KJLocalization;

class CompensationUtils
{

    public static function generateReport($invoice, string $state)
    {
        $today = date('d-m-Y');

        $pdfPaper = $invoice->label->document;

        $locale = App::getLocale();

        $data = array(
            'OutputFolder' => config('documentservice.output_folder') . '\\' . $invoice->getTable() . '\\' . $invoice->ID . '\\Vergoedingsbrieven',
            'Sjabloon' => 'CompensationLetter',
            'ReportName' => KJLocalization::translate('Admin - Facturen', 'Vergoedingsbrief', 'Vergoedingsbrief', [], $locale),
            'Statements' => array(
                array(
                    'Identifier' => "CRM_RELATION",
                    'Query' => "SELECT CR.ID, ISNULL(NUMBER_DEBTOR,'') AS NUMBER_DEBTOR, CR.NAME, CR.VAT_LIABLE, CR.VAT_NUMBER FROM CRM_RELATION CR WITH(NOLOCK) WHERE CR.ID = {1}",
                    'Parameters' => [(int)$invoice->FK_CRM_RELATION]
                ),
                array(
                    'Identifier' => "CRM_RELATION_ADDRESS",
                    'Query' => "SELECT CA.* FROM dbo.CRM_RELATION_ADDRESS CRA WITH (NOLOCK) JOIN CORE_ADDRESS CA WITH (NOLOCK) ON CA.ID = CRA.FK_CORE_ADDRESS WHERE CRA.ID = {1} AND CRA.ACTIVE = 1",
                    'Parameters' => [(int)$invoice->FK_CRM_RELATION_ADDRESS]
                ),
                array(
                    'Identifier' => "COMPENSATION",
                    'Query' => "SELECT ".
                        " '" . $invoice->project->getCompensationPercentageFormattedAttribute() . "' AS PERCENTAGE, " .
                        " '" . $invoice->getCompensatedPriceFormattedAttribute() . "' AS PRICE ",
                    'Parameters' => []
                ),
                array(
                    'Identifier' => "TEXT",
                    'Query' => "SELECT " .
                        " '" . $today . "' AS DATUM, " .
                        " '" . KJLocalization::translate('Admin - Compensatiebrief', 'Hoofdkantoor', 'Doetinchem', [], $locale) . "' AS HOOFDKANTOOR, " .
                        " '" . KJLocalization::translate('Admin - Compensatiebrief', 'Onderwerp', 'Onderwerp', [], $locale) . "' AS ONDERWERP, " .
                        " '" . KJLocalization::translate('Admin - Compensatiebrief', 'Financiële bijdrage interventie', 'Financiële bijdrage interventie', [], $locale) . "' AS FINANCIELE_INTERVENTIE, " .
                        " '" . KJLocalization::translate('Admin - Compensatiebrief', 'Geache heer / mevrouw', 'Geache heer / mevrouw', [], $locale) . "' AS AANHEF, " .
                        " '" . KJLocalization::translate('Admin - Compensatiebrief', 'Wij kunnen u meedelen dat er voor de in te zetten interventie een financiële bijdrage is toegezegd. Hieronder treft u een toelichting.', 'Wij kunnen u meedelen dat er voor de in te zetten interventie een financiële bijdrage is toegezegd. Hieronder treft u een toelichting.', [], $locale) . "' AS TEXTLETTER_BEGIN, " .
                        " '" . KJLocalization::translate('Admin - Compensatiebrief', 'Hoe hoog is de bijdrage?', 'Hoe hoog is de bijdrage?', [], $locale) . "' AS QUESTION, " .
                        " '" . KJLocalization::translate('Admin - Compensatiebrief', 'Bij deze interventie is de vergoeding', 'Bij deze interventie is de vergoeding', [], $locale) . "' AS TEXT_BEFORE_PERCENTAGE, " .
                        " '" . KJLocalization::translate('Admin - Compensatiebrief', 'van het factuurbedrag exclusief BTW, te weten', 'van het factuurbedrag exclusief BTW, te weten', [], $locale) . "' AS TEXT_AFTER_PERCENTAGE, " .
                        " '" . KJLocalization::translate('Admin - Compensatiebrief', 'In de bijlage treft u de factuur. Graag zien wij de betaling binnen 14 dagen tegemoet.', 'In de bijlage treft u de factuur. Graag zien wij de betaling binnen 14 dagen tegemoet.', [], $locale) . "' AS TEXT_AFTER_PRICE, " .
                        " '" . KJLocalization::translate('Admin - Compensatiebrief', 'Heeft u nog vragen naar aanleiding van deze brief? Neemt u dan gerust contact met ons op.', 'Heeft u nog vragen naar aanleiding van deze brief? Neemt u dan gerust contact met ons op.', [], $locale) . "' AS AFSLUITER, " .
                        " '" . KJLocalization::translate('Admin - Compensatiebrief', 'Met vriendelijke groet, ', 'Met vriendelijke groet,', [], $locale) . "' AS GROET," .
                        " '" . KJLocalization::translate('Admin - Compensatiebrief', 'Exsist B.V.', 'Exsist B.V.', [], $locale) . "' AS EXSIST" ,
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
                $invoice->FK_DOCUMENT_COMPENSATION_LETTER = $document->ID;
                $invoice->TS_GENERATE_COMPENSATION_LETTER = new DateTime();
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
}