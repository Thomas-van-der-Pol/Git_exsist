<?php
    $columns = array();
    
    // Overal behalve concept
    if ($type != config('invoice_state_type.TYPE_CONCEPT')) {
        $columns[] = array(
            'field' => 'NUMBER',
            'title' => KJLocalization::translate("Admin - Facturen", "Factuurnummer", "Factuurnummer")
        );
        $columns[] = array(
            'field' => 'DATE_FORMATTED',
            'title' => KJLocalization::translate("Admin - Facturen", "Factuurdatum", "Factuurdatum")
        );
    }
    
    $columns[] = array(
        'field' => 'RELATION',
        'title' => KJLocalization::translate("Admin - Facturen", "Relatie", "Relatie")
    );
    $columns[] = array(
        'field' => 'TOTAL_PRICE',
        'title' => KJLocalization::translate("Admin - Facturen", "Totaal excl", "Totaal excl.")
    );
    $columns[] = array(
        'field' => 'TOTAL_PRICE_INCL',
        'title' => KJLocalization::translate("Admin - Facturen", "Totaal incl", "Totaal incl.")
    );
    
    if ($type == config('invoice_state_type.TYPE_ALL')) {// Alle
        $columns[] = array(
            'field' => 'PAID_FORMATTED',
            'title' => KJLocalization::translate("Admin - Facturen", "Betaald", "Betaald")
        );
    }
    // Alle, open en vervallen
    if (in_array($type, [config('invoice_state_type.TYPE_ALL'), config('invoice_state_type.TYPE_OPEN'), config('invoice_state_type.TYPE_EXPIRED')])) {
        $columns[] = array(
            'field' => 'EXPIRATION_DATE_FORMATTED',
            'title' => KJLocalization::translate("Admin - Facturen", "Vervaldatum", "Vervaldatum")
        );
    }

    // open en vervallen
    if (in_array($type, [config('invoice_state_type.TYPE_OPEN'), config('invoice_state_type.TYPE_EXPIRED')])) {
        $columns[] = array(
            'field' => 'DAYS_FORMATTED',
            'title' => KJLocalization::translate("Admin - Facturen", "Dgn", "Dgn")
        );
    }

    $columns[] = array(
        'field' => 'ADVANCE_FORMATTED',
        'title' => KJLocalization::translate("Admin - Facturen", "Voorschot", "Voorschot")
    );
    
    // Alle facturen, dan kolom status erbij
    if ($type == config('invoice_state_type.TYPE_ALL')) {
        $columns = array_merge($columns, array(array(
            'field' => 'WORKFLOWSTATE',
            'title' => KJLocalization::translate("Admin - Facturen", "Status", "Status")
        )));
    }

    $checkableDescriptionColumn = 'NUMBER';
    if ($type == config('invoice_state_type.TYPE_CONCEPT')) {
        $checkableDescriptionColumn = 'RELATION';
    }
?>

{{ KJDatatable::create(
    'ADM_INVOICE_TABLE_' . $type,
    array (
        'method' => 'GET',
        'url' => '/admin/invoice/allByStateDatatable/' . $type,
        'pagination' => true,
        'sortable' => false,
        'editable' => true,
        'editinline' => false,
        'pagesize' => 50,
        'editURL' => '/admin/invoice/detail/',
        'searchinput' => '#ADM_INVOICE_FILTER_SEARCH',
        'checkable' => true,
        'checkableDescriptionColumn' => $checkableDescriptionColumn,
        'columns' => $columns,
        'filters' => array(
            array(
                'input' => '#ADM_FILTER_INVOICE_DATE',
                'queryParam' => 'TS_INVOICEDATE',
                'default' => NULL
            )
        )
    )
) }}