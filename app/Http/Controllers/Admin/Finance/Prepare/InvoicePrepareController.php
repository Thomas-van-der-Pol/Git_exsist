<?php

namespace App\Http\Controllers\Admin\Finance\Prepare;

use App\Models\Admin\Finance\Billcheck;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use KJ\Core\controllers\AdminBaseController;
use KJ\Localization\libraries\LanguageUtils;
use Yajra\DataTables\Facades\DataTables;
use KJLocalization;

class InvoicePrepareController extends AdminBaseController {

    protected $model = 'App\Models\Admin\Finance\Billcheck';

    protected $mainViewName = 'admin.finance.prepare.main';

    protected $joinClause = [
        [
            'TABLE' => 'CRM_RELATION',
            'PRIMARY_FIELD' => 'ID',
            'FOREIGN_FIELD' => 'FK_CRM_RELATION',
        ],
        [
            'TABLE' => 'FINANCE_INVOICE_COLLECT_INTERVAL',
            'PRIMARY_FIELD' => 'ID',
            'FOREIGN_FIELD' => 'FK_FINANCE_INVOICE_COLLECT_INTERVAL',
        ]
    ];

    protected $datatableDefaultSort = array(
        [
            'field' => 'CRM_RELATION.NAME',
            'sort'  => 'ASC'
        ]
    );

    protected $detailScreenFolder = 'admin.finance.prepare.detail_screens';
    protected $detailViewName = 'admin.finance.prepare.detail';

    protected function authorizeRequest($method, $parameters)
    {
        return Auth::guard()->user()->hasPermission(config('permission.FACTURATIE'));
    }

    public function allDatatable(Request $request)
    {
        $this->allColumns = [
            'FK_CRM_RELATION',
            'CRM_RELATION.NAME AS RELATION',
            DB::raw('CAST(IIF(FK_CRM_CONTACT IS NULL, 1, 0) AS BIT) AS INVALID'),
            'FINANCE_BILLCHECK.FK_FINANCE_INVOICE_COLLECT_INTERVAL',
            'FINANCE_INVOICE_COLLECT_INTERVAL.DESCRIPTION_SHORT',
            'FINANCE_BILLCHECK.FK_PROJECT',
            DB::raw('SUM(PRICE_TOTAL) AS PRICE_TOTAL'),
            DB::raw('SUM(PRICE_TOTAL_INCVAT) AS PRICE_TOTAL_INCVAT'),
            DB::raw("STUFF((SELECT ',' + CONVERT(NVARCHAR, FB_DETAIL.ID) FROM FINANCE_BILLCHECK FB_DETAIL WITH (NOLOCK) WHERE FB_DETAIL.FK_CRM_RELATION = FINANCE_BILLCHECK.FK_CRM_RELATION AND FB_DETAIL.FK_FINANCE_INVOICE_COLLECT_INTERVAL = FINANCE_BILLCHECK.FK_FINANCE_INVOICE_COLLECT_INTERVAL AND FB_DETAIL.FK_PROJECT = FINANCE_BILLCHECK.FK_PROJECT FOR XML PATH, TYPE).value('.[1]','NVARCHAR(MAX)'), 1, 1, '') AS BILLCHECKIDString")
        ];

        return parent::allDatatable($request);
    }

    protected function allInternalModifyItems(&$items)
    {
        $items->groupBy([
            'FK_CRM_RELATION',
            'CRM_RELATION.NAME',
            DB::raw('CAST(IIF(FK_CRM_CONTACT IS NULL, 1, 0) AS BIT)'),
            'FINANCE_BILLCHECK.FK_FINANCE_INVOICE_COLLECT_INTERVAL',
            'FINANCE_INVOICE_COLLECT_INTERVAL.DESCRIPTION_SHORT',
            'FINANCE_BILLCHECK.FK_PROJECT'
        ]);
    }

    protected function beforeDatatable($datatable)
    {
        $datatable->addColumn('PRICE_TOTAL_FORMATTED', function($item) {
                return '€ ' . number_format(($item->PRICE_TOTAL), 2, LanguageUtils::getDecimalPoint(), LanguageUtils::getThousandsSeparator());
            })
            ->addColumn('PRICE_TOTAL_INCVAT_FORMATTED', function($item) {
                return '€ ' . number_format(($item->PRICE_TOTAL_INCVAT), 2, LanguageUtils::getDecimalPoint(), LanguageUtils::getThousandsSeparator());
            })
            ->addColumn('WORKFLOWSTATE', function($item) {
                return ($item->project->workflowstate->DESCRIPTION ?? '');
            })
            ->addColumn('PROJECTNAME', function($item) {
                return ($item->project->title ?? '');
            });
    }

    function customDetailAsJSON(Request $request, $ids)
    {
        $view = view($this->detailViewName)
            ->with('ids', $ids);

        $errors = array();

        if (Billcheck::whereIn('ID', explode(',', $ids))->whereNull('FK_CRM_CONTACT')->count() > 0) {
            array_push($errors, KJLocalization::translate('Admin - Facturen', 'Fout contactpersoon onbekend', 'Contactpersoon onbekend: voer contactpersoon in bij relatie en actualiseer facturen opnieuw'));
        }
        if (Billcheck::whereIn('ID', explode(',', $ids))->whereNull('FK_CRM_RELATION_ADDRESS')->count() > 0) {
            array_push($errors, KJLocalization::translate('Admin - Facturen', 'Fout factuuradres onbekend', 'Factuuradres onbekend: voer factuuradres in bij relatie en actualiseer facturen opnieuw'));
        }

        $view->with('errors', $errors);

        return response()->json([
            'viewDetail' => $view->render()
        ]);
    }

    function allDetailDatatable($ids)
    {
        $items = Billcheck::select([
                'STARTDATE',
                'ENDDATE',
                'QUANTITY_MONTH',
                'DESCRIPTION',
                'PRICE',
                'PRICE_INCVAT',
                DB::raw('SUM(QUANTITY) AS QUANTITY'),
                DB::raw('SUM(PRICE_TOTAL) AS PRICE_TOTAL'),
                DB::raw('SUM(PRICE_TOTAL_INCVAT) AS PRICE_TOTAL_INCVAT'),
            ])
            ->whereIn('ID', explode(',',$ids))
            ->groupBy([
                'STARTDATE',
                'ENDDATE',
                'QUANTITY_MONTH',
                'DESCRIPTION',
                'PRICE',
                'PRICE_INCVAT'
            ])
            ->get();

        return DataTables::collection($items)
            ->addColumn('PERIOD_FORMATTED', function(Billcheck $billcheck) {
                return $billcheck->PeriodFormatted;
            })
            ->addColumn('QUANTITY_FORMATTED', function(Billcheck $billcheck) {
                return $billcheck->QuantityFormatted;
            })
            ->addColumn('PRICE_FORMATTED', function(Billcheck $billcheck) {
                return $billcheck->PriceFormatted;
            })
            ->addColumn('PRICE_TOTAL_FORMATTED', function(Billcheck $billcheck) {
                return $billcheck->PriceTotalFormatted;
            })
            ->addColumn('PRICE_INCVAT_FORMATTED', function(Billcheck $billcheck) {
                return $billcheck->PriceInclFormatted;
            })
            ->addColumn('PRICE_TOTAL_INCVAT_FORMATTED', function(Billcheck $billcheck) {
                return $billcheck->PriceTotalInclFormatted;
            })
            ->make();
    }

    public function process(Request $request)
    {
        $date = DateTime::createFromFormat(LanguageUtils::getDateFormat(), $request->input('DATE'));
        $date = $date->modify('midnight');

        try{
            DB::update('EXEC [FINANCE_BILLCHECK_FILL] ?, ?',[$date, config('dropdown_type.TYPE_ADDRESSTYPE_VALUE.INVOICE')]);

            return response()->json([
                'success' => true
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                'success' => false
            ]);
        }
    }

    public function createInvoices(Request $request)
    {
        $date = DateTime::createFromFormat(LanguageUtils::getDateFormat(), $request->input('DATE'));
        $date = $date->modify('midnight');

        try{
            DB::insert('EXEC [FINANCE_BILLCHECK_CREATEINVOICES] ?, ?, ?', [implode(',',json_decode($request->get('IDS'), true)), $date, config('dropdown_type.TYPE_ADDRESSTYPE_VALUE.INVOICE')]);

            return response()->json([
                'success' => true
            ]);
        }
        catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }

    }
}