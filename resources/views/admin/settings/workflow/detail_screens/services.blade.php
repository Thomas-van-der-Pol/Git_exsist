<div class="kt-portlet__body">
    <div class="kt-form kt-form--label-right">
        <div class="row align-items-center">
            <div class="col order-2 order-xl-2">
                <a href="javascript:;" id="addProducts" class="btn btn-success btn-sm btn-upper pull-right" data-id="{{ $item->ID }}" data-type="{{ config('product_type.TYPE_SERVICE') }}">
                    <i class="fa fa-plus-square"></i>
                    {{ KJLocalization::translate('Admin - Producten & diensten', 'Dienst', 'Dienst')}}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="kt-separator m-0"></div>

{{ KJDatatable::create(
    'ADM_WORKFLOW_PRODUCTS_TABLE',
    array (
        'method' => 'GET',
        'url' => '/admin/settings/workflow/product/allByWorkflowDatatable/' . $item->ID,
        'parentid' => $item->ID,
        'columns' => array(
            array(
                'field' => 'FULL_PRODUCT',
                'title' => KJLocalization::translate('Admin - Producten & diensten', 'Omschrijving intern', 'Omschrijving intern')
            ),
            array(
                'field' => 'PRICE',
                'title' => KJLocalization::translate('Admin - Producten & diensten', 'Prijs', 'Prijs')
            )
        ),
        'customEditButtons' => array(
            'end' => [
                [
                    'HTML'  => '<a href="javascript:;" data-id="{idField}" class="btn btn-bold btn-label-danger btn-sm btn-icon deleteProduct" title="' . KJLocalization::translate("Algemeen", "Verwijderen", "Verwijderen") . '" ><i class="la la-close""></i></a>'
                ]
            ]
        )
    )
) }}