<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Libraries\Admin\InvoiceUtils;
use App\Models\Admin\Core\Label;
use App\Models\Admin\CRM\Address;
use App\Models\Admin\CRM\Relation;
use App\Models\Admin\Finance\Invoice;
use App\Models\Core\WorkflowState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use KJ\Core\controllers\AdminBaseController;
use KJ\Core\libraries\ReportUtils;
use KJLocalization;

class InvoiceController extends AdminBaseController {

    protected $model = 'App\Models\Admin\Finance\Invoice';

    protected $detailScreenFolder = 'admin.finance.invoice.detail_screens';
    protected $detailViewName = 'admin.finance.invoice.detail';

    protected $saveUnsetValues = [
        'RELATION_NAME',
        'CONTACT_NAME',
        'DATE',
        'EXPIRATION_DATE',
        'STATE',
        'TOTAL_EXCL',
        'TOTAL_VAT',
        'TOTAL_INCL',
        'DAYS_REMAINING',
        'PAID',
    ];

    protected function authorizeRequest($method, $parameters)
    {
        return Auth::guard()->user()->hasPermission(config('permission.FACTURATIE'));
    }

    public function beforeDetailScreen(int $id, $item, $screen)
    {
        $none = ['' => KJLocalization::translate('Algemeen', 'Niets geselecteerd', 'Niets geselecteerd') . '..'];

        $bindings = [];

        switch ($screen) {
            case 'default':

                $workflow_statesOri = WorkflowState::where(['ACTIVE' => true, 'FK_CORE_WORKFLOWSTATETYPE' => config('workflowstate_type.TYPE_INVOICE')])->pluck('DESCRIPTION', 'ID')->toArray();
                $workflow_states = $none + $workflow_statesOri;

                $labels = Label::where('ACTIVE', true);
                $default_label = null;
                if ($labels->count() == 1) {
                    $default_label = $labels->first()->ID;
                }
                $labels = $none + $labels->pluck('DESCRIPTION', 'ID')->toArray();

                $contactsOri = [];
                $addressesOri = [];
                if ($item && $item->relation) {
                    $contactsOri = $item->relation->contacts()->where('ACTIVE', true)->pluck('FULLNAME', 'ID')->toArray();
                    $addressesOri = Address::all()
                        ->where('ACTIVE', true)
                        ->where('FK_CRM_RELATION', $item->FK_CRM_RELATION)
                        ->where('FK_CORE_DROPDOWNVALUE_ADRESSTYPE', config('dropdown_type.TYPE_ADDRESSTYPE_VALUE.INVOICE'))
                        ->pluck('fullAddress', 'ID')
                        ->toArray();
                }
                $contacts = $none + $contactsOri;
                $addresses = $none + $addressesOri;

                $bindings = array_merge($bindings, [
                    ['workflow_states', $workflow_states],
                    ['labels', $labels],
                    ['default_label', $default_label],
                    ['contacts', $contacts],
                    ['addresses', $addresses],
                ]);
                break;
        }

        return $bindings;
    }

    public function save(Request $request)
    {
        $this->saveExtraValues = [];

        if ($request->get('ID') == $this->newRecordID) {
            $this->saveExtraValues = [
                'FK_CORE_WORKFLOWSTATE' => config('workflowstate.INVOICE_CONCEPT'),
                'MANUAL' => true
            ];
        }

        return parent::save($request);
    }

    protected function previewPDF(Request $request, int $ID)
    {
        // Opgevraagde factuur
        $invoice = Invoice::find($ID);

        if ($invoice->document && !$request->has('renew')) {
            return ReportUtils::getFile($invoice->document->FILEPATH);
        } else {
            $document = InvoiceUtils::generateReport($ID, '');

            // Alleen als succes
            if ($document['success']) {
                // response file
                return ReportUtils::getFile($document['file']['filename']);
            }

            // Anders error
            return response()->json([
                'success' => false,
                'Error' => $document['file']['resultText']
            ], $document['file']['statusCode']);
        }
    }

//    public function setFinal(Request $request)
//    {
//        $id = $request->get('ID');
//        return InvoiceUtils::sendInvoice($id);
//    }

    public function sendInvoice(Request $request)
    {
        $id = $request->get('ID');
        return InvoiceUtils::sendInvoice($id);
    }

    public function sendInvoiceReminder(Request $request)
    {
        $id = $request->get('ID');
        return InvoiceUtils::sendInvoiceReminder($id);
    }

    public function delete(int $id)
    {
        $item = $this->find($id);

        if ($item) {
            if (
                (($item->NUMBER ?? 0) > 0) or
                ($item->FK_CORE_WORKFLOWSTATE != config('workflowstate.INVOICE_CONCEPT'))
            ) {
                return response()->json([
                    'success' => false,
                    'message' => KJLocalization::translate('Admin - Facturen', 'Alleen handmatige conceptfacturen kunnen worden verwijderd', 'Alleen handmatige conceptfacturen kunnen worden verwijderd!')
                ], 200);
            }

            $result = $item->delete();

            return response()->json([
                'success' => $result,
                'message' => KJLocalization::translate('Admin - Facturen', 'Factuur verwijderd', 'Factuur verwijderd!')
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => KJLocalization::translate('Algemeen', 'Item niet (meer) gevonden', 'Item niet (meer) gevonden')
            ]);
        }
    }

    public function createAdvance(Request $request)
    {
        $project_id = $request->get('ID');
        $type = $request->get('TYPE');
        $percentage = $request->get('PERCENTAGE');
        $amount = $request->get('AMOUNT');

        try{
            $result = collect(DB::select('EXEC [FINANCE_CREATE_ADVANCE_INVOICE] ?, ?, ?, ?, ?', [$project_id, $type, $percentage, $amount, config('dropdown_type.TYPE_ADDRESSTYPE_VALUE.INVOICE')]))->first();

            return response()->json([
                'success' => true,
                'id' => $result->ID
            ]);
        }
        catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }

    }
}