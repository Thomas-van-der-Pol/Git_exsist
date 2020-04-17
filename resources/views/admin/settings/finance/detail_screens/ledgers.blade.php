<div class="kt-portlet__body">
    <div class="kt-form kt-form--label-right">
        <div class="row align-items-center">
            <div class="col order-2 order-xl-2">
                <a href="javascript:;" id="addLedger" class="btn btn-success btn-sm btn-upper pull-right">
                    <i class="fa fa-plus-square"></i>
                    {{ KJLocalization::translate('Admin - Financieel', 'Grootboekrekening', 'Grootboekrekening')}}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="kt-separator m-0"></div>

{{ KJDatatable::create(
    'ADM_LABEL_LEDGER_TABLE',
    array (
        'method' => 'GET',
        'url' => '/admin/settings/finance/ledger/allByLabelDatatable/' . $item->ID,
        'parentid' => $item->ID,
        'editable' => true,
        'editURL' => '/admin/settings/finance/ledger/detailRendered/',
        'addable' => true,
        'addButton' => '#addLedger',
        'saveURL' => '/admin/settings/finance/ledger',
        'columns' => array(
            array(
                'field' => 'DESCRIPTION',
                'title' => KJLocalization::translate('Admin - Financieel', 'Omschrijving', 'Omschrijving')
            ),
            array(
                'field' => 'ACCOUNT',
                'title' => KJLocalization::translate('Admin - Financieel', 'Grootboeknummer', 'Grootboeknummer')
            )
        ),
        'customEditButtons' => array(
            'end' => [
                [
                    'HTML'  => '<a href="javascript:;" data-id="{idField}" class="btn btn-bold btn-label-danger btn-sm btn-icon deleteLedger" title="' . KJLocalization::translate("Algemeen", "Verwijderen", "Verwijderen") . '" ><i class="la la-close""></i></a>'
                ]
            ]
        )
    )
) }}