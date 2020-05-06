<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Libraries\Admin\InvoiceUtils;
use App\Models\Admin\Finance\Invoice;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use KJ\Core\controllers\AdminBaseController;
use KJ\Core\libraries\SessionUtils;
use KJ\Localization\libraries\LanguageUtils;
use KJLocalization;

class InvoiceOverviewController extends AdminBaseController {

    protected $model = 'App\Models\Admin\Finance\Invoice';

    protected $mainViewName = 'admin.finance.invoice.main';

    protected $allColumns = [
        'ID', 'ACTIVE', 'FK_CRM_RELATION', 'FK_CORE_WORKFLOWSTATE', 'DATE', 'EXPIRATION_DATE', 'NUMBER', 'PAID',
        'PRICE_TOTAL_EXCL', 'PRICE_TOTAL_INCL', 'VAT_TOTAL', 'FK_PROJECT', 'IS_ADVANCE', 'CRM_RELATION.NAME',
        'CORE_WORKFLOWSTATE.DESCRIPTION'
    ];

    protected $joinClause = [
        [
            'TABLE' => 'CRM_RELATION',
            'PRIMARY_FIELD' => 'ID',
            'FOREIGN_FIELD' => 'FK_CRM_RELATION',
        ],
        [
            'TABLE' => 'CORE_WORKFLOWSTATE',
            'PRIMARY_FIELD' => 'ID',
            'FOREIGN_FIELD' => 'FK_CORE_WORKFLOWSTATE',
        ],
    ];

    protected $detailScreenFolder = 'admin.finance.invoice.overview_screens';

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

    protected function beforeDatatable($datatable)
    {
        $datatable->addColumn('DATE_FORMATTED', function (Invoice $invoice) {
                return $invoice->DateFormatted;
            })
            ->addColumn('EXPIRATION_DATE_FORMATTED', function (Invoice $invoice) {
                return $invoice->ExpirationDateFormatted;
            })
            ->addColumn('RELATION', function (Invoice $invoice) {
                return isset($invoice->relation) ? $invoice->relation->title : '';
            })
            ->addColumn('WORKFLOWSTATE', function (Invoice $invoice) {
                return isset($invoice->workflowstate) ? $invoice->workflowstate->DESCRIPTION : '';
            })
            ->addColumn('TOTAL_PRICE', function (Invoice $invoice) {
                return $invoice->TotalExclFormatted;
            })
            ->addColumn('TOTAL_PRICE_INCL', function (Invoice $invoice) {
                return $invoice->TotalInclFormatted;
            })
            ->addColumn('PAID_FORMATTED', function (Invoice $invoice) {
                return $invoice->PaidFormatted;
            })
            ->addColumn('ADVANCE_FORMATTED', function (Invoice $invoice) {
                return $invoice->AdvanceFormatted;
            })
            ->addColumn('DAYS_FORMATTED', function (Invoice $invoice) {
                return $invoice->getDaysRemaining();
            });
    }

    public function allByStateDatatable(Request $request, int $ID)
    {
        $this->whereClause = [];
        $this->whereInClause = [];

        switch ($ID) {
            // Alle statussen (geen filter)
            case config('invoice_state_type.TYPE_ALL'):
                $this->whereClause = array();
                break;

            // 1 = Concept
            case config('invoice_state_type.TYPE_CONCEPT'):
                $this->whereClause = array(
                    ['FK_CORE_WORKFLOWSTATE', config('workflowstate.INVOICE_CONCEPT')]
                );

                $this->datatableDefaultSort = array(
                    [
                        'sort' => 'ASC',
                        'field' => 'CRM_RELATION.NAME'
                    ],
                    [
                        'sort' => 'DESC',
                        'field' => 'DATE'
                    ]
                );

                break;

            // 2 = Openstaand
            case config('invoice_state_type.TYPE_OPEN'):
                $this->whereClause = array(
                    ['PAID', false],
                    ['FK_CORE_WORKFLOWSTATE', config('workflowstate.INVOICE_FINAL')]
                );
                break;

            // 3 = Vervallen
            case config('invoice_state_type.TYPE_EXPIRED'):
                $this->whereClause = array(
                    ['PAID', false],
                    ['FK_CORE_WORKFLOWSTATE', config('workflowstate.INVOICE_FINAL')]
                );
                $this->whereRawClause = array(
                    ['EXPIRATION_DATE <= GETDATE()', null],
                );
                break;

            // 4 = Betaald
            case config('invoice_state_type.TYPE_PAID'):
                $this->whereClause = array(
                    ['PAID', true],
                    ['FK_CORE_WORKFLOWSTATE', config('workflowstate.INVOICE_FINAL')]
                );
                break;
        }

        $start = SessionUtils::getSession('ADM_INVOICE', 'ADM_FILTER_INVOICE_DATE_startDate', '');
        $end = SessionUtils::getSession('ADM_INVOICE', 'ADM_FILTER_INVOICE_DATE_endDate', '');

        $defaultDate = [];
        if ($start != '' && $end != '') {
            $startDate = DateTime::createFromFormat(LanguageUtils::getDateFormat(), $start);
            $endDate = DateTime::createFromFormat(LanguageUtils::getDateFormat(), $end);

            $defaultDate = array(
                'default' => [
                    'start' => $startDate->format('Y-m-d'),
                    'end' => $endDate->format('Y-m-d')
                ]
            );
        }

        $this->datatableFilter = array(
            ['DATE', array_merge(array(
                'param' => 'TS_INVOICEDATE',
                'operation' => 'daterange'
            ), $defaultDate)],
            ['CRM_RELATION.NAME, NUMBER, DATE', array(
                'param' => 'ADM_INVOICE_FILTER_SEARCH',
                'operation' => 'like',
                'default' => ''
            )],
        );

        return parent::allDatatable($request);
    }

    public function allByRelationDatatable(Request $request, int $ID)
    {
        $this->whereClause = array(
            ['FK_CRM_RELATION', $ID],
            ['ACTIVE', true]
        );

        return parent::allDatatable($request);
    }

    public function allByProjectDatatable(Request $request, int $ID)
    {
        $this->whereClause = array(
            ['FK_PROJECT', $ID],
            ['ACTIVE', true]
        );

        return parent::allDatatable($request);
    }

    public function generateBulk(Request $request)
    {
        $ids = json_decode($request->get('ids'), true);

        for ($i = 0; $i < count($ids); $i++) {
            InvoiceUtils::sendInvoice($ids[$i], true);
        }

        return response()->json([
            'success' => true,
            'message' => KJLocalization::translate('Admin - Facturen', 'Facturen verstuurd', 'Facturen verstuurd')
        ], 200);
    }

    public function reminderBulk(Request $request)
    {
        $ids = json_decode($request->get('ids'), true);

        for ($i = 0; $i < count($ids); $i++) {
            InvoiceUtils::sendInvoiceReminder($ids[$i]);
        }

        return response()->json([
            'success' => true,
            'message' => KJLocalization::translate('Admin - Facturen', 'Herinneringen verstuurd', 'Herinneringen verstuurd')
        ], 200);
    }
}