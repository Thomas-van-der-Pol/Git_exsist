<?php

namespace App\Http\Controllers\Admin\Settings\Workflow\Product;

use App\Models\Core\WorkflowStateType;
use Illuminate\Http\Request;
use KJ\Core\controllers\AdminBaseController;
use KJLocalization;

class ProductWorkflowController extends AdminBaseController
{
    protected $model = 'App\Models\Core\WorkflowProduct';

    protected $allColumns = ['ID', 'ACTIVE', 'FK_CORE_WORKFLOWSTATETYPE', 'FK_ASSORTMENT_PRODUCT'];

    protected $joinClause = [
        [
            'TABLE' => 'ASSORTMENT_PRODUCT',
            'PRIMARY_FIELD' => 'ID',
            'FOREIGN_FIELD' => 'FK_ASSORTMENT_PRODUCT',
        ]
    ];

    protected $datatableDefaultSort = array(
        [
            'field' => 'ASSORTMENT_PRODUCT.DESCRIPTION_INT',
            'sort'  => 'ASC'
        ]
    );

    protected function beforeDatatable($datatable)
    {
        $datatable->addColumn('FULL_PRODUCT', function($item) {
                return ( $item->product ? $item->product->DESCRIPTION_INT : '' );
            })
            ->addColumn('PRICE', function($item) {
                return ( $item->product ? $item->product->getPriceFormattedAttribute() : '' );
            });
    }

    public function allByWorkflowDatatable(Request $request, int $ID)
    {
        $this->whereClause = array(
            ['FK_CORE_WORKFLOWSTATETYPE', $ID],
            ['ACTIVE', true]
        );

        return parent::allDatatable($request);
    }

    protected function addProduct(Request $request)
    {
        $id = ( $request->get('id') ?? 0 );
        $product = json_decode(( $request->get('product') ?? 0 ), true);

        $workflowstate_type = WorkflowStateType::find($id);
        $createProduct = $workflowstate_type->createProduct($product);

        return response()->json([
            'success' => ($createProduct != null)
        ]);
    }

    public function delete(int $ID)
    {
        $item = $this->find($ID);

        if ($item) {
            // Delete
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