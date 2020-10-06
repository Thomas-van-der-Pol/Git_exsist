{{ KJDatatable::create(
    'ADM_PRODUCT_MODAL_TABLE',
    [
        'method' => 'GET',
        'url' => '/admin/product/allDatatable',
        'checkable' => ((request('checkable') ?? 0) == 1),
        'checkableDescriptionColumn' => 'DESCRIPTION_INT',
        'selectable' => ((request('selectable') ?? 0) == 1),
        'columns' => [
            [
                'field' => 'DESCRIPTION_INT',
                'title' => KJLocalization::translate('Admin - Interventies', 'Omschrijving intern', 'Omschrijving intern')
            ],
        ]
    ]
) }}

@if(((request('show_options') ?? 0) == 1))
    {{ KJField::date('STARTDATE', KJLocalization::translate('Admin - Taken', 'Start datum', 'Start datum'), (date(KJ\Localization\libraries\LanguageUtils::getDateFormat(), strtotime(date("D M d")))), ['required', 'data-locale-format' => \KJ\Localization\libraries\LanguageUtils::getJSDatePickerFormat()]) }}
    {{ KJField::text('QUOTATION_NUMBER', KJLocalization::translate('Admin - Facturen', 'Offertenummer', 'Offertenummer'), '', true, []) }}
    {{ KJField::select('FK_CORE_USER_ASSIGNEE', KJLocalization::translate('Admin - Taken', 'Standaard taken interventie toewijzen aan', 'Standaard taken interventie toewijzen aan'), $contacts, '', true, 0, ['required']) }}
@endif