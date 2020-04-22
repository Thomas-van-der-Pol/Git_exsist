<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Models\Admin\Assortment\Product;
use App\Models\Admin\Finance\InvoiceScheme;
use App\Models\Admin\Project\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use KJ\Core\controllers\AdminBaseController;
use KJLocalization;

class InvoiceSchemeController extends AdminBaseController {

    protected $model = 'App\Models\Admin\Finance\InvoiceScheme';

    protected $allColumns = ['ID','FK_ASSORTMENT_PRODUCT', 'ACTIVE', 'FK_PROJECT_ASSORTMENT_PRODUCT', 'FK_FINANCE_INVOICE_LINE', 'DAYS', 'DATE', 'PERCENTAGE', 'AUTOMATIC_REMNANT'];

    protected $detailViewName = 'admin.finance.invoice.invoice_scheme.detail';

    protected $saveParentIDField = 'FK_ASSORTMENT_PRODUCT';

    protected function authorizeRequest($method, $parameters)
    {
        return (Auth::guard()->user()->hasPermission(config('permission.FACTURATIE')) && Auth::guard()->user()->hasPermission(config('permission.PRODUCTEN_DIENSTEN'))) ;
    }

    protected function beforeDatatable($datatable)
    {
        $datatable->addColumn('PERCENTAGE', function(InvoiceScheme $invoiceScheme) {
            return $invoiceScheme->getPercentageFormattedAttribute();
        });
    }

    public function allByProductDatatable(Request $request, int $ID)
    {
        $this->whereClause = [];
        $this->datatableFilter = [];

        if ($ID > 0) {
            $this->whereClause = array(
                ['FK_ASSORTMENT_PRODUCT', $ID],
                ['FK_PROJECT_ASSORTMENT_PRODUCT', null]
            );
        }

        return parent::allDatatable($request);
    }

    public function save(Request $request)
    {
        $product = Product::find($request->get('PARENTID'));
        $total = $product->invoiceSchemes->where('AUTOMATIC_REMNANT', false);

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
        $product = Product::find($item->FK_ASSORTMENT_PRODUCT);
        $product->checkRemnant();
    }

    public function delete(int $ID)
    {
        $item = $this->find($ID);

        if ($item) {
            // Get product
            $product = $item->product;

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