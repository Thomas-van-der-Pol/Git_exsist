{{ KJDatatable::create(
    'ADM_ACCOUNTANCY_DEBTOR_TABLE',
    [
        'method' => 'GET',
        'url' => '/admin/accountancy/debtor/allDatatable',
        'pagination' => true,
        'sortable' => true,
        'editable' => false,
        'editinline' => false,
        'checkable' => true,
        'searchinput' => '#ADM_ACCOUNTANCY_FILTER_SEARCH',
        'columns' => [
            [
                'field' => 'DEBTORNUMBER',
                'title' => KJLocalization::translate('Admin - Boekhouding', 'Debiteurnummer', 'Debiteurnummer')
            ],
            [
                'field' => 'NAME',
                'title' => KJLocalization::translate('Admin - Boekhouding', 'Bedrijfsnaam', 'Bedrijfsnaam')
            ],
            [
                'field' => 'PHONENUMBER',
                'title' => KJLocalization::translate('Admin - Boekhouding', 'Telefoonnummer', 'Telefoonnummer')
            ],
            [
                'field' => 'EMAILADDRESS',
                'title' => KJLocalization::translate('Admin - Boekhouding', 'E-mailadres', 'E-mailadres')
            ],
            [
                'field' => 'INVOICE_ADDRESS',
                'title' => KJLocalization::translate('Admin - Boekhouding', 'Factuuradres', 'Factuuradres')
            ],
            [
                'field' => 'VISIT_ADDRESS',
                'title' => KJLocalization::translate('Admin - Boekhouding', 'Bezoekadres', 'Bezoekadres')
            ],
            [
                'field' => 'EXACT_DEBTOR_ERROR',
                'title' => KJLocalization::translate('Admin - Boekhouding', 'Foutmelding', 'Foutmelding')
            ]
        ]
    ]
) }}