<?php

namespace App\Http\Controllers\Admin\Assortment;

use App\Libraries\Core\DropdownvalueUtils;
use App\Models\Admin\Assortment\Product;
use App\Models\Admin\Assortment\ProductType;
use App\Models\Admin\Finance\InvoiceScheme;
use App\Models\Admin\Finance\Ledger;
use App\Models\Admin\Finance\VAT;
use App\Models\Core\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use KJ\Core\controllers\AdminBaseController;
use KJ\Core\libraries\SessionUtils;
use KJLocalization;

class ProductController extends AdminBaseController
{
    protected $model = 'App\Models\Admin\Assortment\Product';

    protected $mainViewName = 'admin.assortment.product.main';

    protected $allColumns = ['ID', 'ACTIVE', 'FK_ASSORTMENT_PRODUCT_TYPE', 'DESCRIPTION_INT', 'DESCRIPTION_EXT', 'PRICE', 'PRICE_INCVAT'];

    protected $datatableFilter = [
        ['ACTIVE', array(
            'param' => 'ACTIVE',
            'default' => true
        )],
    ];

    protected $datatableDefaultSort = array(
        [
            'field' => 'DESCRIPTION_INT',
            'sort'  => 'ASC'
        ]
    );

    protected $saveUnsetValues = [
        'requester_table',
        'requester_item',
        'PRICE_READ'
    ];

    protected $detailScreenFolder = 'admin.assortment.product.detail_screens';
    protected $detailViewName = 'admin.assortment.product.detail';

    protected $exceptAuthorization = ['indexRendered', 'allByTypeDatatable'];

    protected function authorizeRequest($method, $parameters)
    {
        return Auth::guard()->user()->hasPermission(config('permission.PRODUCTEN_DIENSTEN'));
    }

    protected function beforeIndex()
    {
        $none = ['' => KJLocalization::translate('Algemeen', 'Niets geselecteerd', 'Niets geselecteerd') . '..'];

        $producttypesOri = ProductType::all()->where('ACTIVE', true)->sortBy('title')->pluck('title', 'ID');
        $producttypes = $none + $producttypesOri->toArray();

        $status = DropdownvalueUtils::getStatusDropdown(false);

        $bindings = array(
            ['producttypes', $producttypes],
            ['status', $status]
        );

        return $bindings;
    }

    public function indexRendered()
    {
        $view = view('admin.assortment.product.modal');

        $extraBindings = $this->beforeIndex();
        if ($extraBindings != []) {
            foreach ($extraBindings as $binding) {
                $view->with($binding[0], $binding[1]);
            }
        }

        return response()->json([
            'viewDetail' => $view->render()
        ]);
    }

    public function beforeDetailScreen(int $id, $item, $screen)
    {
        $bindings = [];
        $none = ['' => KJLocalization::translate('Algemeen', 'Niets geselecteerd', 'Niets geselecteerd') . '..'];

        switch ($screen) {
            case 'default':
                $producttypesOri = ProductType::all()->where('ACTIVE', true)->sortBy('title')->pluck('title', 'ID');
                $producttypes = $none + $producttypesOri->toArray();

                $ledgersOri = Ledger::select(DB::raw("CONCAT(ACCOUNT,' - ',DESCRIPTION) AS COMBINEDDESCRIPTION"),'ID','ACTIVE', 'FK_CORE_LABEL')
                    ->where('ACTIVE', TRUE)
                    ->orderBy('COMBINEDDESCRIPTION')
                    ->pluck('COMBINEDDESCRIPTION', 'ID');
                $ledgers = $none + $ledgersOri->toArray();

                $vatOri = VAT::where('ACTIVE', true)
                    ->orderBy('DESCRIPTION')
                    ->pluck('DESCRIPTION', 'ID');
                $vat = $none + $vatOri->toArray();

                $bindings = array_merge($bindings, [
                    ['producttypes', $producttypes],
                    ['ledgers', $ledgers],
                    ['vat', $vat],
                ]);
                break;
        }

        return $bindings;
    }

    protected function beforeDatatable($datatable)
    {
        $datatable->addColumn('PRICE_FORMATTED', function(Product $product) {
            return $product->getPriceFormattedAttribute();
        })->addColumn('PRICE_INCVAT_FORMATTED', function(Product $product) {
            return $product->getPriceIncFormattedAttribute();
        })->addColumn('TYPE_PRODUCT', function(Product $product) {
            return isset($product->producttype) ? $product->producttype->title : '';
        });
    }

    public function allDefaultDatatable(Request $request)
    {
        $this->datatableFilter = [
            ['DESCRIPTION_INT, DESCRIPTION_EXT', array(
                'param' => 'ADM_FILTER_PRODUCT',
                'operation' => 'like',
                'default' => ''
            )],
            ['ACTIVE', array(
                'param' => 'ACTIVE',
                'default' => true
            )],
            ['FK_ASSORTMENT_PRODUCT_TYPE', array(
                'param' => 'ASSORTMENT_PRODUCT_TYPE',
                'default' => SessionUtils::getSession('ADM_ASSORTMENT', 'ADM_FILTER_PRODUCT_PRODUCTTYPE', '')
            )]
        ];

        return parent::allDatatable($request);
    }

    protected function afterSave($item, $originalItem, Request $request, &$response)
    {
        $id = $request->get('ID');
        if($id == -1){
            $invoiceScheme = new InvoiceScheme([
                'ACTIVE' => true,
                'FK_ASSORTMENT_PRODUCT' => $item->ID,
                'DAYS' => 14,
                'PERCENTAGE' => 100,
                'AUTOMATIC_REMNANT' => true
            ]);
            $invoiceScheme->save();
        }
    }

    public function allByTypeDatatable(Request $request, int $ID)
    {
        $this->whereClause = [
            ['ACTIVE', true]
        ];

        if ($ID > 0) {
            array_push($this->whereClause, ['FK_ASSORTMENT_PRODUCT_TYPE', $ID]);
        }

        $this->datatableFilter = [
            ['DESCRIPTION_INT, DESCRIPTION_EXT', array(
                'param' => 'ADM_FILTER_PRODUCT',
                'operation' => 'like',
                'default' => ''
            )],
            ['FK_ASSORTMENT_PRODUCT_TYPE', array(
                'param' => 'FK_ASSORTMENT_PRODUCT_TYPE',
                'default' => ''
            )]
        ];

        return parent::allDatatable($request);
    }

    public function delete(int $id)
    {
        $item = $this->find($id);

        if ($item) {
            if ($item->ACTIVE) {
                $status = 'gearchiveerd';
            } else {
                $status = 'geactiveerd';
            }

            $item->ACTIVE = !$item->ACTIVE;
            $result = $item->save();

            return response()->json([
                'success' => $result,
                'message' => KJLocalization::translate('Algemeen', 'Item kon niet worden ' . $status, 'Item kon niet worden ' . $status)
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => KJLocalization::translate('Algemeen', 'Item niet (meer) gevonden', 'Item niet (meer) gevonden')
            ]);
        }
    }

    public function uploadFile(Request $request)
    {
        $result = false;

        $id = $request->get('id');
        $item = $this->find($id);

        $file = $request->file('file');
        if ($file) {
            // Save in storage
            $fullpathsavedfile = $file->storeAs('/'.$item->getTable().'/'.$item->ID, $file->getClientOriginalName(), ['disk' => 'ftp']);
            $accessableFile = str_replace('public/','storage/', $fullpathsavedfile);

            // Get file info
            $documentInfo = pathinfo($accessableFile);

            // Link to model
            $document = new Document([
                'FK_TABLE' => $item->getTable(),
                'FK_ITEM' => $item->ID,
                'UPLOADER_FK_TABLE' => Auth::guard()->user()->getTable(),
                'UPLOADER_FK_ITEM' => Auth::guard()->user()->ID,
                'FILEPATH' => str_replace('/', '\\', $accessableFile),
                'FILESIZE' => $file->getSize(),
                'TITLE' => $documentInfo['filename'],
                'FILETYPE' => ($file->getClientOriginalExtension() ?? 'file')
            ]);

            $result = $document->save();

            if ($result) {
                $item->FK_DOCUMENT = $document->ID;
                $item->save();
            }
        }

        return response()->json([
            'success' => $result,
            'document' => ($document ?? null)
        ]);
    }

    public function deleteFile(int $id)
    {
        $document = Document::find($id);
        $item = $this->find($document->FK_ITEM);

        if ($document) {
            $item->FK_DOCUMENT = null;
            $item->save();

            $document->delete();

            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false
            ]);
        }
    }

}