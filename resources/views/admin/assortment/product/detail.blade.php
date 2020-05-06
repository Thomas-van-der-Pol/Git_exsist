@extends('theme.demo1.main', ['title' => $item ? $item->title : KJLocalization::translate('Admin - Interventies', 'Nieuwe interventie', 'Nieuwe interventie')])

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
            ],
            [
                'title' => $item ? $item->title : KJLocalization::translate('Admin - Interventies', 'Nieuwe interventie', 'Nieuwe interventie'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/product/detail/' . ( $item ? $item->ID : -1 )), '/')
            ]
        ]
    ])
    @endcomponent
@endsection

@section('content')
    <div class="row">
        @component('portlet::main', ['notitle' => true, 'colsize' => 12, 'portletClass' => 'kt-portlet--height-fluid'])
            <div class="kt-widget kt-widget--user-profile-3" id="default" data-id="{{ ( $item ? $item->ID : -1 ) }}">

            </div>
        @endcomponent
    </div>

    @if($item)
        <div class="row">
            @component('portlet::main', ['notitle' => true, 'colsize' => 3])
                <div class="kt-widget kt-widget--user-profile-1 pb-0">
                    <div class="kt-widget__body">
                        <div class="kt-widget__items nav" role="tablist">
                            <a href="#invoice_scheme" data-id="{{ $item->ID }}" class="kt-widget__item kt-widget__item--active" data-toggle="tab" role="tab" aria-selected="true">
                                <span class="kt-widget__section">
                                    <span class="kt-widget__icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <polygon points="0 0 24 0 24 24 0 24"/>
                                                <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                                <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                                            </g>
                                        </svg>
                                    </span>
                                    <span class="kt-widget__desc">
                                        {{ KJLocalization::translate('Admin - Interventies', 'Facturatieschema', 'Facturatieschema') }}
                                    </span>
                                </span>
                            </a>
                            <a href="#tasks" data-id="{{ $item->ID }}" class="kt-widget__item" data-toggle="tab" role="tab" aria-selected="true">
                                <span class="kt-widget__section">
                                    <span class="kt-widget__icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <polygon points="0 0 24 0 24 24 0 24"/>
                                                <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                                <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero"/>
                                            </g>
                                        </svg>
                                    </span>
                                    <span class="kt-widget__desc">
                                        {{ KJLocalization::translate('Admin - Interventies', 'Taken', 'Taken') }}
                                    </span>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            @endcomponent

            <div class="col-lg-9">
                <div class="kt-portlet kt-portlet--mobile kt-portlet--height-fluid" id="detailScreenContainer">
                    <div class="kt-portlet__body kt-portlet__body--fit">
                        <div class="tab-content">
                            <div class="tab-pane active" id="invoice_scheme" data-id="{{ $item->ID }}" role="tabpanel"></div>
                            <div class="tab-pane" id="tasks" data-id="{{ $item->ID }}" data-type="{{ config('task_type.TYPE_PRODUCT') }}" role="tabpanel"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('page-resources')
    {!! Html::script('/assets/custom/js/admin/assortment/product/detail.js?v='.Cache::get('cache_version_number')) !!}
    {!! Html::script('/assets/custom/js/admin/tasks/shared.js?v='.Cache::get('cache_version_number')) !!}
@endsection