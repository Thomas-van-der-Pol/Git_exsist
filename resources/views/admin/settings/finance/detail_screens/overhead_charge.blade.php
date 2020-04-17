<div class="kt-portlet__body">
    <div class="kt-form kt-form--label-right">
        <div class="row align-items-center">
            <div class="col order-2 order-xl-2">
                <a href="javascript:;" id="addOverheadCharge" class="btn btn-success btn-sm btn-upper pull-right">
                    <i class="fa fa-plus-square"></i>
                    {{ KJLocalization::translate('Admin - Financieel', 'Kosten', 'Kosten')}}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="kt-separator m-0"></div>

{{ KJDatatable::create(
    'ADM_LABEL_OVERHEAD_CHARGE_TABLE',
    array (
        'method' => 'GET',
        'url' => '/admin/settings/finance/overhead_charge/allByLabelDatatable/' . $item->ID,
        'parentid' => $item->ID,
        'editable' => true,
        'editURL' => '/admin/settings/finance/overhead_charge/detailRendered/',
        'addable' => true,
        'addButton' => '#addOverheadCharge',
        'saveURL' => '/admin/settings/finance/overhead_charge',
        'columns' => array(
            array(
                'field' => 'DATE_START_FORMATTED',
                'title' => KJLocalization::translate('Admin - Financieel', 'Van', 'Van')
            ),
            array(
                'field' => 'DATE_END_FORMATTED',
                'title' => KJLocalization::translate('Admin - Financieel', 'Tot', 'Tot')
            ),
            array(
                'field' => 'PERCENTAGE_FORMATTED',
                'title' => KJLocalization::translate('Admin - Financieel', 'Percentage', 'Percentage')
            )
        ),
        'customEditButtons' => array(
            'end' => [
                [
                    'HTML'  => '<a href="javascript:;" data-id="{idField}" class="btn btn-bold btn-label-danger btn-sm btn-icon deleteOverheadCharge" title="' . KJLocalization::translate("Algemeen", "Verwijderen", "Verwijderen") . '" ><i class="la la-close""></i></a>'
                ]
            ]
        )
    )
) }}