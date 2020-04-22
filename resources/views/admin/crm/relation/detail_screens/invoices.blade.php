@php
    $editable = false;
    if(Auth::guard()->user()->hasPermission(config('permission.FACTURATIE'))) {
        $editable = true;
    }
@endphp

{{ KJDatatable::create(
    'ADM_RELATION_INVOICE_TABLE',
    array (
        'method' => 'GET',
        'url' => '/admin/crm/invoice/allByRelationDatatable/' . $item->ID,
        'pagination' => true,
        'sortable' => false,
        'editable' => $editable,
        'editinline' => false,
        'pagesize' => 50,
        'editURL' => '/admin/invoice/detail/',
        'columns' => array(
            array(
                'field' => 'NUMBER',
                'title' => KJLocalization::translate("Admin - Facturen", "Factuurnummer", "Factuurnummer")
            ),
            array(
                'field' => 'DATE_FORMATTED',
                'title' => KJLocalization::translate("Admin - Facturen", "Factuurdatum", "Factuurdatum")
            ),
            array(
                'field' => 'TOTAL_PRICE',
                'title' => KJLocalization::translate("Admin - Facturen", "Totaal excl", "Totaal excl.")
            ),
            array(
                'field' => 'TOTAL_PRICE_INCL',
                'title' => KJLocalization::translate("Admin - Facturen", "Totaal incl", "Totaal incl.")
            ),
            array(
                'field' => 'PAID_FORMATTED',
                'title' => KJLocalization::translate("Admin - Facturen", "Betaald", "Betaald")
            ),
            array(
                'field' => 'EXPIRATION_DATE_FORMATTED',
                'title' => KJLocalization::translate("Admin - Facturen", "Vervaldatum", "Vervaldatum")
            ),
            array(
                'field' => 'DAYS_FORMATTED',
                'title' => KJLocalization::translate("Admin - Facturen", "Dgn", "Dgn")
            )
        )
    )
) }}