<div class="kt-portlet__body">
    <div class="kt-form kt-form--label-right">
        <div class="row align-items-center">
            <div class="col order-2 order-xl-2">
                <a href="javascript:;" id="addPaymentTerm" class="btn btn-success btn-sm btn-upper pull-right">
                    <i class="fa fa-plus-square"></i>
                    {{ KJLocalization::translate('Admin - Financieel', 'Betalingsconditie', 'Betalingsconditie')}}
                </a>
            </div>
        </div>
    </div>
</div>

<div class="kt-separator m-0"></div>

{{ KJDatatable::create(
    'ADM_LABEL_PAYMENT_TERM_TABLE',
    array (
        'method' => 'GET',
        'url' => '/admin/settings/finance/payment-term/allByLabelDatatable/' . $item->ID,
        'parentid' => $item->ID,
        'editable' => true,
        'editURL' => '/admin/settings/finance/payment-term/detailRendered/',
        'addable' => true,
        'addButton' => '#addPaymentTerm',
        'saveURL' => '/admin/settings/finance/payment-term',
        'columns' => array(
            array(
                'field' => 'DESCRIPTION',
                'title' => KJLocalization::translate('Admin - Financieel', 'Omschrijving', 'Omschrijving')
            ),
            array(
                'field' => 'AMOUNT_DAYS',
                'title' => KJLocalization::translate('Admin - Financieel', 'Aantal dagen', 'Aantal dagen')
            ),
            array(
                'field' => 'CODE',
                'title' => KJLocalization::translate('Admin - Financieel', 'Code', 'Code')
            ),
            array(
                'field' => 'DEFAULT_FORMATTED',
                'title' => KJLocalization::translate('Admin - Financieel', 'Standaard', 'Standaard')
            )
        ),
        'customEditButtons' => array(
            'end' => [
                [
                    'HTML'  => '<a href="javascript:;" data-id="{idField}" class="btn btn-bold btn-label-danger btn-sm btn-icon deletePaymentTerm" title="' . KJLocalization::translate("Algemeen", "Verwijderen", "Verwijderen") . '" ><i class="la la-close""></i></a>'
                ]
            ]
        )
    )
) }}