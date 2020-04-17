{{ KJDatatable::create(
    'ADM_DROPDOWNVALUE_SELECT_TABLE',
    array (
        'method' => 'GET',
        'url' => '/admin/dropdownvalue/allByTypeDatatable/' . app('request')->input('typeid'),
        'editable' => false,
        'addable' => false,
        'pagination' => true,
        'checkable' => true,
        'checkableDescriptionColumn' => 'VALUE',
        'columns' => array(
            array(
                'field' => 'VALUE',
                'title' => KJLocalization::translate('Algemeen', 'Value', 'Value')
            ),
            array(
                'field' => 'SEQUENCE',
                'title' => KJLocalization::translate('Algemeen', 'Volgorde', 'Volgorde') ,
                'width' => 70
            )
        )
    )
) }}