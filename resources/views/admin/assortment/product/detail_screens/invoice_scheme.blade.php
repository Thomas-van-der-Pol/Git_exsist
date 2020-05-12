<div class="kt-portlet__body">
    <div class="kt-form kt-form--label-right">
        <div class="row align-items-center">
            <div class="col order-2 order-xl-2">
                <a href="javascript:;" id="addInvoiceMoment" class="btn btn-success btn-sm btn-upper pull-right">
                    <i class="fa fa-plus-square"></i>
                    {{ KJLocalization::translate('Admin - Product & diensten', 'Factuurmoment', 'Factuurmoment')}}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="kt-separator m-0"></div>

{{ KJDatatable::create(
    'ADM_PRODUCT_INVOICE_SCHEME_TABLE',
    array (
        'method' => 'GET',
        'url' => '/admin/invoice/scheme/allByProductDatatable/' . $item->ID,
        'editable' => true,
        'parentid' => $item->ID,
        'editURL' => '/admin/invoice/scheme/detailRendered/',
        'addable' => true,
        'addButton' => '#addInvoiceMoment',
        'saveURL' => '/admin/invoice/scheme',
        'columns' => array(
            array(
                'field' => 'DAYS',
                'title' => KJLocalization::translate('Admin - Interventies', 'Dagen tellen tot facturatie datum', 'Dagen tellen tot facturatie datum'),

            ),
            array(
                'field' => 'PERCENTAGE',
                'title' => KJLocalization::translate('Admin - Interventies', 'Percentage', 'Percentage')
            )
        ),
        'customEditButtons' => array(
            'end' => [
                [
                    'HTML' => '<a href="javascript:;" data-id="{idField}" class="btn btn-bold btn-label-danger btn-sm btn-icon deleteInvoiceMoment" title="'.KJLocalization::translate('Algemeen', 'Verwijderen', 'Verwijderen').'" ><i class="la la-close""></i></a>'
                ]
            ]
        )
    )
) }}