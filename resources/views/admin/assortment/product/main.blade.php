@extends('theme.demo1.main', ['title' => KJLocalization::translate('Admin - Menu', 'Interventies', 'Interventies')])

@section('subheader')
    @component('breadcrums::main', [
        'rootURL'       => \KJ\Localization\libraries\LanguageUtils::getUrl('admin'),
        'parentTitle' => KJLocalization::translate('Admin - Menu', 'Interventies', 'Interventies'),
        'items' => [
            [
                'title' => KJLocalization::translate('Algemeen', 'Home', 'Home'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin'), '/')
            ],
            [
                'title' => KJLocalization::translate('Admin - Menu', 'Interventies', 'Interventies'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/product'), '/')
            ]
        ]
    ])
    @endcomponent
@endsection

@section('content')
    <div class="row">
        @component('portlet::main', ['title' => KJLocalization::translate('Admin - Menu', 'Interventies', 'Interventies'), 'colsize' => 12])
            @slot('headicon')
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"/>
                        <path d="M5.5,2 L18.5,2 C19.3284271,2 20,2.67157288 20,3.5 L20,6.5 C20,7.32842712 19.3284271,8 18.5,8 L5.5,8 C4.67157288,8 4,7.32842712 4,6.5 L4,3.5 C4,2.67157288 4.67157288,2 5.5,2 Z M11,4 C10.4477153,4 10,4.44771525 10,5 C10,5.55228475 10.4477153,6 11,6 L13,6 C13.5522847,6 14,5.55228475 14,5 C14,4.44771525 13.5522847,4 13,4 L11,4 Z" fill="#000000" opacity="0.3"/>
                        <path d="M5.5,9 L18.5,9 C19.3284271,9 20,9.67157288 20,10.5 L20,13.5 C20,14.3284271 19.3284271,15 18.5,15 L5.5,15 C4.67157288,15 4,14.3284271 4,13.5 L4,10.5 C4,9.67157288 4.67157288,9 5.5,9 Z M11,11 C10.4477153,11 10,11.4477153 10,12 C10,12.5522847 10.4477153,13 11,13 L13,13 C13.5522847,13 14,12.5522847 14,12 C14,11.4477153 13.5522847,11 13,11 L11,11 Z M5.5,16 L18.5,16 C19.3284271,16 20,16.6715729 20,17.5 L20,20.5 C20,21.3284271 19.3284271,22 18.5,22 L5.5,22 C4.67157288,22 4,21.3284271 4,20.5 L4,17.5 C4,16.6715729 4.67157288,16 5.5,16 Z M11,18 C10.4477153,18 10,18.4477153 10,19 C10,19.5522847 10.4477153,20 11,20 L13,20 C13.5522847,20 14,19.5522847 14,19 C14,18.4477153 13.5522847,18 13,18 L11,18 Z" fill="#000000"/>
                    </g>
                </svg>
            @endslot
        
            @slot('headtools')
                <div class="kt-portlet__head-wrapper">
                    <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/product/detail/-1') }}" id="newProduct" class="btn btn-success btn-sm btn-upper">
                        <i class="fa fa-plus-square"></i>
                        {{ KJLocalization::translate('Admin - Interventies', 'Toevoegen', 'Toevoegen')}}
                    </a>
                </div>
            @endslot

            <div class="kt-form kt-form--label-right">
                <div class="row align-items-center">
                    <div class="col-12 order-2 order-xl-1">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="form-inline md-form filter-icon">
                                    {{ Form::text(
                                        'ADM_FILTER_PRODUCT',
                                        \KJ\Core\libraries\SessionUtils::getSession('ADM_ASSORTMENT', 'ADM_FILTER_PRODUCT', ''),
                                        array(
                                            'class'         => 'form-control filter hasSessionState',
                                            'placeholder'   => KJLocalization::translate('Algemeen', 'Zoeken', 'Zoeken').'...',
                                            'id'            => 'ADM_FILTER_PRODUCT',
                                            'data-module'   => 'ADM_ASSORTMENT',
                                            'data-key'      => 'ADM_FILTER_PRODUCT'
                                        )
                                    ) }}
                                </div>
                            </div>

                            <div class="col-2">
                                <div class="kt-form__group kt-form__group--inline">
                                    <div class="kt-form__label">
                                        {{ Form::label('ADM_FILTER_PRODUCT_STATUS', KJLocalization::translate('Algemeen', 'Status', 'Status'). ':') }}
                                    </div>
                                    <div class="kt-form__control">
                                        {{ Form::select(
                                            'ADM_FILTER_PRODUCT_STATUS',
                                            $status,
                                            \KJ\Core\libraries\SessionUtils::getSession('ADM_ASSORTMENT', 'ADM_FILTER_PRODUCT_STATUS', 1),
                                            [
                                                'class' => 'form-control filter kt-bootstrap-select hasSessionState',
                                                'id'            => 'ADM_FILTER_PRODUCT_STATUS',
                                                'data-module'   => 'ADM_ASSORTMENT',
                                                'data-key'      => 'ADM_FILTER_PRODUCT_STATUS'
                                            ]
                                        ) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @slot('datatable')
                <div class="kt-separator m-0"></div>

                {{ KJDatatable::create(
                    'ADM_PRODUCT_TABLE',
                    array (
                        'method' => 'GET',
                        'url' => '/admin/product/allDatatable',
                        'editable' => true,
                        'editinline' => false,
                        'editURL' => \KJ\Localization\libraries\LanguageUtils::getUrl('admin/product/detail/'),
                        'searchinput' => '#ADM_FILTER_PRODUCT',
                        'saveURL' => '/admin/product',
                        'columns' => array(
                            array(
                                'field' => 'DESCRIPTION_INT',
                                'title' => KJLocalization::translate('Admin - Interventies', 'Omschrijving intern', 'Omschrijving intern')
                            ),
                            array(
                                'field' => 'DESCRIPTION_EXT',
                                'title' => KJLocalization::translate('Admin - Interventies', 'Omschrijving extern', 'Omschrijving extern'),
                                'width' => 350
                            ),
                            array(
                                'field' => 'PRICE_FORMATTED',
                                'title' => KJLocalization::translate('Admin - Interventies', 'Prijs excl', 'Prijs excl'),
                            ),
                            array(
                                'field' => 'PRICE_INCVAT_FORMATTED',
                                'title' => KJLocalization::translate('Admin - Interventies', 'Prijs incl', 'Prijs incl'),
                            )
                        ),
                        'filters' => array(
                            array(
                                'input' => '#ADM_FILTER_PRODUCT_STATUS',
                                'queryParam' => 'ACTIVE',
                                'default' => \KJ\Core\libraries\SessionUtils::getSession('ADM_ASSORTMENT', 'ADM_FILTER_PRODUCT_STATUS', 1)
                            )
                        )
                    )
                ) }}
            @endslot
        @endcomponent
    </div>

@endsection

@section('page-resources')

@endsection