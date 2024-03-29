<div class="kt-portlet__body">
    <div class="kt-form kt-form--label-right">
        <div class="row align-items-center">
            <div class="col order-2 order-xl-2">
                <a href="javascript:;" id="addVat" class="btn btn-success btn-sm btn-upper pull-right">
                    <i class="fa fa-plus-square"></i>
                    {{ KJLocalization::translate('Admin - Financieel', 'Btw', 'Btw')}}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="kt-separator m-0"></div>

{{ KJDatatable::create(
    'ADM_LABEL_VAT_TABLE',
    array (
        'method' => 'GET',
        'url' => '/admin/settings/finance/vat/allByLabelDatatable/' . $item->ID,
        'parentid' => $item->ID,
        'editable' => true,
        'editURL' => '/admin/settings/finance/vat/detailRendered/',
        'addable' => true,
        'addButton' => '#addVat',
        'saveURL' => '/admin/settings/finance/vat',
        'columns' => array(
            array(
                'field' => 'DESCRIPTION',
                'title' => KJLocalization::translate('Admin - Financieel', 'Omschrijving', 'Omschrijving')
            ),
            array(
                'field' => 'PERCENTAGE_FORMATTED',
                'title' => KJLocalization::translate('Admin - Financieel', 'Percentage', 'Percentage')
            ),
            array(
                'field' => 'VATCODE',
                'title' => KJLocalization::translate('Admin - Financieel', 'Btw code', 'Btw code')
            )
        ),
        'customEditButtons' => array(
            'end' => [
                [
                    'HTML'  => '<a href="javascript:;" data-id="{idField}" class="btn btn-bold btn-label-danger btn-sm btn-icon deleteVat" title="' . KJLocalization::translate("Algemeen", "Verwijderen", "Verwijderen") . '" ><i class="la la-close""></i></a>'
                ]
            ]
        )
    )
) }}