<?php

namespace App\Http\Controllers\Admin\Project\Product;

use App\Models\Admin\Project\Project;
use App\Models\Admin\User;
use Illuminate\Http\Request;
use KJ\Core\controllers\AdminBaseController;
use KJ\Localization\libraries\LanguageUtils;
use KJLocalization;

class ProductProjectController extends AdminBaseController
{
    protected $model = 'App\Models\Admin\Project\Product';

    protected $allColumns = ['ID', 'ACTIVE', 'FK_PROJECT', 'FK_ASSORTMENT_PRODUCT', 'FK_CRM_RELATION', 'QUANTITY', 'PRICE'];

    protected $detailViewName = 'admin.project.product.detail';

    protected $joinClause = [
        [
            'TABLE' => 'ASSORTMENT_PRODUCT',
            'PRIMARY_FIELD' => 'ID',
            'FOREIGN_FIELD' => 'PROJECT_ASSORTMENT_PRODUCT.FK_ASSORTMENT_PRODUCT',
        ],
        [
            'TABLE' => 'CRM_RELATION',
            'PRIMARY_FIELD' => 'ID',
            'FOREIGN_FIELD' => 'PROJECT_ASSORTMENT_PRODUCT.FK_CRM_RELATION',
        ]
    ];

    protected $saveUnsetValues = [
        'PROVIDER_NAME'
    ];

    protected function beforeDatatable($datatable)
    {
        $datatable->addColumn('FULL_PRODUCT', function($item) {
                return ( $item->product ? $item->product->DESCRIPTION_INT : '' );
            })
            ->addColumn('RELATION', function($item) {
                return ( $item->relation ? $item->relation->title : '' );
            })
            ->addColumn('BLOCKED', function($item) {
                $allInvoiceSchemes = $item->invoiceSchemes->whereNotNull('FK_FINANCE_INVOICE_LINE')->count();
                return ($allInvoiceSchemes > 0) ? true : false;
            })
            ->addColumn('PRICE', function($item) {
                return $item->getPriceFormattedAttribute();
            })
            ->addColumn('PRICE_TOTAL', function($item) {
                return $item->getTotalPriceFormattedAttribute();
            });
    }

    public function allByProjectProductDatatable(Request $request, int $ID)
    {
        $this->datatableFilter = array(
            ['ASSORTMENT_PRODUCT.DESCRIPTION_INT, ASSORTMENT_PRODUCT.DESCRIPTION_EXT, CRM_RELATION.NAME', array(
                'param' => 'ADM_PROJECT_PRODUCT_FILTER_SEARCH',
                'operation' => 'like',
                'default' => ''
            )]
        );

        $this->whereClause = array(
            ['ACTIVE', true],
            ['FK_PROJECT', $ID],
            ['ASSORTMENT_PRODUCT.FK_ASSORTMENT_PRODUCT_TYPE', config('product_type.TYPE_PRODUCT')]
        );

        return parent::allDatatable($request);
    }

    public function allByProjectProductTotal(Request $request, int $ID)
    {
        $this->datatableFilter = array(
            ['ASSORTMENT_PRODUCT.DESCRIPTION_INT, ASSORTMENT_PRODUCT.DESCRIPTION_EXT, CRM_RELATION.NAME', array(
                'param' => 'ADM_PROJECT_PRODUCT_FILTER_SEARCH',
                'operation' => 'like',
                'default' => ''
            )]
        );

        $this->whereClause = array(
            ['ACTIVE', true],
            ['FK_PROJECT', $ID],
            ['ASSORTMENT_PRODUCT.FK_ASSORTMENT_PRODUCT_TYPE', config('product_type.TYPE_PRODUCT')]
        );

        $items = $this->allInternal(
            $request,
            false
        );

        $total = $items->sum(function($item) {
            return $item->QUANTITY * $item->PRICE;
        });

        return response()->json([
            'success' => true,
            'total' => '€ ' . number_format($total, 2, LanguageUtils::getDecimalPoint(), LanguageUtils::getThousandsSeparator())
        ]);
    }

    protected function addProduct(Request $request)
    {
        $assignee = User::find($request->get('assignee'));

        $startDate = $request->get('date');
        $id = ( $request->get('id') ?? 0 );
        $product = json_decode(( $request->get('product') ?? 0 ), true);
        $project = Project::find($id);

        $createProduct = $project->createProduct($product, $startDate, $assignee);

        return response()->json([
            'success' => ($createProduct != null)
        ]);
    }

    public function delete(int $ID)
    {
        $item = $this->find($ID);

        if ($item) {

            // Delete all invoice moments of this intervention-dossier
            foreach ($item->invoiceSchemes as $invoiceScheme){
                $invoiceScheme->delete();
            }

            // Delete all task of this intervention-dossier
            foreach ($item->tasks as $task){
                $task->delete();
            }
            //delete intervention-dossier.
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