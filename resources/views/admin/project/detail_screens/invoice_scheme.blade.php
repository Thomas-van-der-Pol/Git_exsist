<div class="kt-portlet__body">
    <div class="kt-form kt-form--label-right">
        <div class="row align-items-center">
            <div class="col order-2 order-xl-2">
                <a href="javascript:;" id="addInvoiceMomentProject" class="btn btn-success btn-sm btn-upper pull-right" data-pid="{{ $item->ID }}">
                    <i class="fa fa-plus-square"></i>
                    {{ KJLocalization::translate('Admin - Product & diensten', 'Factuurmoment', 'Factuurmoment')}}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="kt-separator m-0"></div>

{{ KJDatatable::create(
    'ADM_PROJECT_INVOICE_SCHEME_TABLE',
    array (
        'method' => 'GET',
        'url' => '/admin/invoice/scheme/project/allByProductDatatable/' . $item->ID,
        'editable' => true,
        'parentid' => $item->ID,
        'editURL' => '/admin/invoice/scheme/project/detailRendered/',
        'addable' => true,
        'addButton' => '#addInvoiceMomentProject',
        'saveURL' => '/admin/invoice/scheme/project',
        'blockEditColumn' => 'BLOCKED',
        'columns' => array(
            array(
                'field' => 'DATE',
                'title' => KJLocalization::translate('Admin - Dossiers', 'Datum', 'Datum'),
            ),
            array(
                'field' => 'INTERVENTION',
                'title' => KJLocalization::translate('Admin - Dossiers', 'Interventie', 'Interventie')
            ),
            array(
                'field' => 'PERCENTAGE',
                'title' => KJLocalization::translate('Admin - Dossiers', 'Percentage', 'Percentage')
            ),
            array(
                'field' => 'INTERVENTION_PRICE',
                'title' => KJLocalization::translate('Admin - Dossiers', 'Stukprijs interventie', 'Stukprijs interventie')
            ),
            array(
                'field' => 'SUBTOTAL_PERCENTAGE',
                'title' => KJLocalization::translate('Admin - Dossiers', 'Subtotaal', 'Subtotaal')
            ),
            array(
                'field' => 'INVOICE_NUMBER',
                'title' => KJLocalization::translate('Admin - Dossiers', 'Factuurnummer', 'Factuurnummer')
            )
        ),
        'customEditButtons' => array(
            'end' => [
                [
                    'HTML' => '<a href="javascript:;" data-id="{idField}" class="btn btn-bold btn-label-danger btn-sm btn-icon deleteInvoiceMomentProject" title="'.KJLocalization::translate('Algemeen', 'Verwijderen', 'Verwijderen').'" ><i class="la la-close""></i></a>'
                ]
            ]
        )
    )
) }}