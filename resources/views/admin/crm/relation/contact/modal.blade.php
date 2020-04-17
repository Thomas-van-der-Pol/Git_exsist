{{ KJDatatable::create(
    'ADM_RELATION_CONTACT_MODAL_TABLE',
    [
        'method' => 'GET',
        'url' => '/admin/crm/relation/contact/allByRelationDatatable/' . (request('relation') ?? 0),
        'editable' => false,
        'checkable' => true,
        'checkableDescriptionColumn' => 'FULLNAME',
        'columns' => [
            [
                'field' => 'FULLNAME',
                'title' => KJLocalization::translate('Admin - CRM', 'Naam', 'Naam')
            ],
            [
                'field' => 'EMAILADDRESS',
                'title' => KJLocalization::translate('Admin - CRM', 'E-mail', 'E-mail')
            ],
            [
                'field' => 'PHONENUMBER',
                'title' => KJLocalization::translate('Admin - CRM', 'Telefoon', 'Telefoon')
            ]
        ]
    ]
) }}