<?php

namespace App\Http\Controllers\Admin\Project\Product;

use App\Models\Admin\Assortment\Product;
use App\Models\Admin\Finance\InvoiceScheme;
use App\Models\Admin\Project\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use KJ\Core\controllers\AdminBaseController;
use Yajra\DataTables\DataTables;
use KJLocalization;

class InvoiceSchemeController extends AdminBaseController {

    protected $model = 'App\Models\Admin\Finance\InvoiceScheme';

    protected $allColumns = ['ID','FK_ASSORTMENT_PRODUCT', 'ACTIVE', 'FK_PROJECT_ASSORTMENT_PRODUCT', 'FK_FINANCE_INVOICE_LINE', 'DAYS', 'DATE', 'PERCENTAGE', 'AUTOMATIC_REMNANT'];

    protected $detailViewName = 'admin.project.invoice_scheme.detail';

//    protected $saveParentIDField = 'FK_PROJECT_ASSORTMENT_PRODUCT';

    protected function authorizeRequest($method, $parameters)
    {
        return (Auth::guard()->user()->hasPermission(config('permission.FACTURATIE')) && Auth::guard()->user()->hasPermission(config('permission.PRODUCTEN_DIENSTEN'))) ;
    }

    protected $saveUnsetValues = [
        'FK_ASSORTMENT_PRODUCT',
        'INTERVENTION_PRICE',
        'SUBTOTAL_PERCENTAGE',
        'INVOICE_NUMBER',
    ];

    protected function beforeDatatable($datatable)
    {
        $datatable->addColumn('DATUM', function(InvoiceScheme $invoiceScheme) {
            return $invoiceScheme->getDateFormattedAttribute();
        })
        ->addColumn('BLOCKED', function(InvoiceScheme $invoiceScheme) {

            $allInvoiceSchemes = $invoiceScheme->project_product->invoiceSchemes->whereNotNull('FK_FINANCE_INVOICE_LINE')->count();
            return ($allInvoiceSchemes > 0) ? true : false;

        })
        ->addColumn('INTERVENTION', function(InvoiceScheme $invoiceScheme) {
            return $invoiceScheme->product->title;
        })
        ->addColumn('PERCENTAGE', function(InvoiceScheme $invoiceScheme) {
            return $invoiceScheme->getPercentageFormattedAttribute();
        })
        ->addColumn('INTERVENTION_PRICE', function(InvoiceScheme $invoiceScheme) {
            return $invoiceScheme->product->getPriceFormattedAttribute();
        })
        ->addColumn('SUBTOTAL_PERCENTAGE', function(InvoiceScheme $invoiceScheme) {
            return $invoiceScheme->getPricePercentageFormattedAttribute();
        })
        ->addColumn('INVOICE_NUMBER', function(InvoiceScheme $invoiceScheme) {
            if($invoiceScheme->invoiceLine) {
                return $invoiceScheme->invoiceLine->invoice->NUMBER;
            }
        });
    }

    public function allByProjectProductDatatable(Request $request, int $ID)
    {
        $this->whereClause = [];
        $this->datatableFilter = [];
        $invoiceSchemesOfProject = [];
        if ($ID > 0) {
            $productsOfProject = Project::find($ID)->products;
            foreach($productsOfProject as $product){
                foreach($product->invoiceSchemes as $invoiceScheme){
                    array_push($invoiceSchemesOfProject, $invoiceScheme);
                }
            }
        }
        $datatable = Datatables::of($invoiceSchemesOfProject);
        $this->beforeDatatable($datatable);
        return $datatable->make(true);
    }

    protected function beforeDetail(int $ID, $item)
    {
        $pid = request('pid') ? request('pid') : $item->FK_PROJECT;

        $none = ['' => KJLocalization::translate('Algemeen', 'Niets geselecteerd', 'Niets geselecteerd') . '..'];
        $products = \App\Models\Admin\Project\Product::with('product')
            ->where([
                'ACTIVE' => true,
                'FK_PROJECT' => $pid
            ])
            ->get()
            ->pluck('product.DESCRIPTION_INT', 'ID')
            ->toArray();
        $products = $none + $products;

        $bindings =  [
            ['products', $products]
        ];

        return $bindings;
    }

    private function getProjectProduct(Request $request){
        $assortmentProduct = Product::find($request->get('FK_ASSORTMENT_PRODUCT'));
        $project = Project::find($request->get('PARENTID'));
        $projectProduct = null;
        foreach($project->products as $product){
            if($product->FK_ASSORTMENT_PRODUCT == $assortmentProduct->ID){
                $projectProduct = $product;
            }
        }
        return $projectProduct;
    }

    public function save(Request $request)
    {
        $productForTotal = null;
        if ($request->get('ID') != $this->newRecordID) {
            $invoiceScheme = InvoiceScheme::find($request->get('ID'));
            $productForTotal = $invoiceScheme->project_product;
        }
        else{
            $productForTotal = $this->getProjectProduct(($request));
        }

        $total = $productForTotal->invoiceSchemes->where('AUTOMATIC_REMNANT', false);
        if ($request->get('ID') != $this->newRecordID) {
            $total = $total->where('ID', '<>', $request->get('ID'));
        }

        $total = $total->sum('PERCENTAGE');
        $total = $total + (float)$request->get('PERCENTAGE');
        if ($total > 100) {
            return response()->json([
                'success' => false,
                'message' => KJLocalization::translate('Admin - Dossiers', 'Door het ingevoerde percentage komt u boven de 100% uit', 'Door het ingevoerde percentage komt u boven de 100% uit')
            ]);
        }

        return parent::save($request);
    }

    protected function afterSave($item, $originalItem, Request $request, &$response)
    {
        // Renew remnant row if changed
        if (($item->AUTOMATIC_REMNANT == true) && (($originalItem->PERCENTAGE ?? 0) != $item->PERCENTAGE)) {
            $item->AUTOMATIC_REMNANT = false;
            $item->save();
        }
        // Check remnant
        if(!$originalItem){
            $projectProduct = $this->getProjectProduct($request);
            if($projectProduct){
                $item->FK_PROJECT_ASSORTMENT_PRODUCT = $projectProduct->ID;
                $item->FK_ASSORTMENT_PRODUCT = $projectProduct->product->ID;
                $item->save();
                $item->refresh();
            }
        }

        $product = $item->project_product;
        $product->checkRemnant();
    }

    public function delete(int $ID)
    {
        $item = $this->find($ID);

        if ($item) {

            if($item->invoiceLine){
                if($item->invoiceLine->invoice->FK_CORE_WORKFLOWSTATE == config('workflowstate.INVOICE_FINAL')){
                    return response()->json([
                        'success' => false,
                        'message' => KJLocalization::translate('Admin - Dossiers', 'Item is al gefactureerd', 'Item is al gefactureerd'),
                    ], 200);
                }
            }

            // Get product
            $product = $item->project_product;

            //delete concept invoice line if exsist
            if($item->invoiceLine){
                $item->invoiceLine->delete();
            }
            // Delete invoice moment
            $item->delete();

            // Check invoice scheme remnant
            $product->checkRemnant();

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