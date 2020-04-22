<div class="kt-form kt-form--label-right">
    <div class="row align-items-center">
        <div class="col-auto order-2 order-xl-1">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="form-inline md-form filter-icon">
                        {{ Form::text(
                            'ADM_FILTER_PRODUCT',
                            '',
                            [
                                'class'         => 'form-control filter',
                                'placeholder'   => KJLocalization::translate('Algemeen', 'Zoeken', 'Zoeken').'...',
                                'id'            => 'ADM_FILTER_PRODUCT',
                            ]
                        ) }}
                    </div>
                </div>
                @if((request('type') ?? 0) == 0)
                    <div class="col-auto">
                        <div class="kt-form__group kt-form__group--inline">
                            <div class="kt-form__label">
                                {{ Form::label('ADM_FILTER_PRODUCT_PRODUCTTYPE', KJLocalization::translate('Admin - Dossiers', 'Producttype', 'Producttype'). ':') }}
                            </div>
                            <div class="kt-form__control">
                                {{ Form::select(
                                    'ADM_FILTER_PRODUCT_PRODUCTTYPE',
                                    $producttypes,
                                    '',
                                    [
                                        'class' => 'form-control filter kt-bootstrap-select'
                                    ]
                                ) }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>




{{ KJDatatable::create(
    'ADM_PRODUCT_MODAL_TABLE',
    [
        'method' => 'GET',
        'url' => '/admin/product/allByTypeDatatable/' . (request('type') ?? 0),
        'searchinput' => '#ADM_FILTER_PRODUCT',
        'checkable' => ((request('checkable') ?? 0) == 1),
        'checkableDescriptionColumn' => 'DESCRIPTION_INT',
        'selectable' => ((request('selectable') ?? 0) == 1),
        'columns' => [
            [
                'field' => 'TYPE_PRODUCT',
                'title' => KJLocalization::translate('Admin - Dossiers', 'Type product', 'Type product')
            ],
            [
                'field' => 'DESCRIPTION_INT',
                'title' => KJLocalization::translate('Admin - Dossiers', 'Omschrijving intern', 'Omschrijving intern')
            ],
        ],
        'filters' => [
            [
                'input' => '#ADM_FILTER_PRODUCT_PRODUCTTYPE',
                'queryParam' => 'FK_ASSORTMENT_PRODUCT_TYPE',
                'default' => ''
            ]
        ]
    ]
) }}

@if((request('type') ?? 0) == 1)
    {{ KJField::date('STARTDATE', KJLocalization::translate('Admin - Taken', 'Start datum', 'Start datum'), (date(KJ\Localization\libraries\LanguageUtils::getDateFormat(), strtotime(date("D M d")))), ['required', 'data-date-start-date' => '-0d', 'data-locale-format' => \KJ\Localization\libraries\LanguageUtils::getJSDatePickerFormat()]) }}
@endif