{{ KJDatatable::create(
    'ADM_ACCOUNTANCY_INVOICE_TABLE',
    [
        'method' => 'GET',
        'url' => '/admin/accountancy/invoice/allDatatable',
        'pagination' => true,
        'sortable' => true,
        'editable' => true,
        'editURL' => '/admin/accountancy/invoice/detailRendered/',
        'checkable' => true,
        'checkableDescriptionColumn' => 'NUMBER',
        'searchinput' => '#ADM_ACCOUNTANCY_FILTER_SEARCH',
        'columns' => [
            [
                'field' => 'DEBTORNUMBER',
                'title' => KJLocalization::translate('Admin - Boekhouding', 'Debiteurnummer', 'Debiteurnummer')
            ],
            [
                'field' => 'NUMBER',
                'title' => KJLocalization::translate('Admin - Boekhouding', 'Factuurnummer', 'Factuurnummer')
            ],
            [
                'field' => 'DESCRIPTION',
                'title' => KJLocalization::translate('Admin - Boekhouding', 'Omschrijving', 'Omschrijving')
            ],
            [
                'field' => 'DATE_FORMATTED',
                'title' => KJLocalization::translate('Admin - Boekhouding', 'Datum', 'Datum')
            ],
            [
                'field' => 'PRICE_TOTAL_EXCL_FORMATTED',
                'title' => KJLocalization::translate('Admin - Boekhouding', 'Totaal excl', 'Totaal excl.')
            ],
            [
                'field' => 'PRICE_TOTAL_INCL_FORMATTED',
                'title' => KJLocalization::translate('Admin - Boekhouding', 'Totaal incl', 'Totaal incl.')
            ],
            [
                'field' => 'EXACT_INVOICE_ERROR',
                'title' => KJLocalization::translate('Admin - Boekhouding', 'Foutmelding', 'Foutmelding')
            ]
        ]
    ]
) }}