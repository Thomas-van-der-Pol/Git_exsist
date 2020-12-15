@extends('theme.demo1.main', ['title' => $item ? $item->title : KJLocalization::translate('Admin - Financieel', 'Nieuwe administratie', 'Nieuwe administratie')])

@section('subheader')
    @component('breadcrums::main', [
        'rootURL'       => \KJ\Localization\libraries\LanguageUtils::getUrl('admin'),
        'parentTitle' => KJLocalization::translate('Admin - Menu', 'Financieel', 'Financieel'),
        'items' => [
            [
                'title' => KJLocalization::translate('Algemeen', 'Home', 'Home'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin'), '/')
            ],
            [
                'title' => KJLocalization::translate('Admin - Menu', 'Instellingen', 'Instellingen'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/group/1'), '/')
            ],
            [
                'title' => KJLocalization::translate('Admin - Menu', 'Financieel', 'Financieel'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/finance'), '/')
            ],
            [
                'title' => $item ? $item->title : KJLocalization::translate('Admin - Financieel', 'Nieuwe administratie', 'Nieuwe administratie'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/finance/detail/' . ($item->ID ?? -1)), '/')
            ]
        ]
    ])
    @endcomponent
@endsection

@section('content')
    <div class="row">
        @component('portlet::main', ['notitle' => true, 'colsize' => 12])
            <div class="kt-widget kt-widget--user-profile-3" id="default" data-id="{{ $item->ID ?? -1 }}"></div>
        @endcomponent
    </div>

    @if($item)
        <div class="row">
            @component('portlet::main', ['notitle' => true, 'colsize' => 3])
                <div class="kt-widget kt-widget--user-profile-1 pb-0">
                    <div class="kt-widget__body">
                        <div class="kt-widget__items nav" role="tablist">
                            <a href="#ledgers" data-id="{{ $item->ID }}" class="kt-widget__item kt-widget__item--active" data-toggle="tab" role="tab" aria-selected="true">
                                <span class="kt-widget__section">
                                    <span class="kt-widget__icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24"/>
                                                <path d="M4,4 L20,4 C21.1045695,4 22,4.8954305 22,6 L22,18 C22,19.1045695 21.1045695,20 20,20 L4,20 C2.8954305,20 2,19.1045695 2,18 L2,6 C2,4.8954305 2.8954305,4 4,4 Z" fill="#000000" opacity="0.3"/>
                                                <path d="M18.5,11 L5.5,11 C4.67157288,11 4,11.6715729 4,12.5 L4,13 L8.58578644,13 C8.85100293,13 9.10535684,13.1053568 9.29289322,13.2928932 L10.2928932,14.2928932 C10.7456461,14.7456461 11.3597108,15 12,15 C12.6402892,15 13.2543539,14.7456461 13.7071068,14.2928932 L14.7071068,13.2928932 C14.8946432,13.1053568 15.1489971,13 15.4142136,13 L20,13 L20,12.5 C20,11.6715729 19.3284271,11 18.5,11 Z" fill="#000000"/>
                                                <path d="M5.5,6 C4.67157288,6 4,6.67157288 4,7.5 L4,8 L20,8 L20,7.5 C20,6.67157288 19.3284271,6 18.5,6 L5.5,6 Z" fill="#000000"/>
                                            </g>
                                        </svg>
                                    </span>
                                    <span class="kt-widget__desc">
                                        {{ KJLocalization::translate('Admin - Financieel', 'Grootboekrekeningen', 'Grootboekrekeningen') }}
                                    </span>
                                </span>
                            </a>

                            <a href="#vat" data-id="{{ $item->ID }}" class="kt-widget__item" data-toggle="tab" role="tab" aria-selected="false">
                                <span class="kt-widget__section">
                                    <span class="kt-widget__icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24"/>
                                                <path d="M16.0322024,5.68722152 L5.75790403,15.945742 C5.12139076,16.5812778 5.12059836,17.6124773 5.75613416,18.2489906 C5.75642891,18.2492858 5.75672377,18.2495809 5.75701875,18.2498759 L5.75701875,18.2498759 C6.39304347,18.8859006 7.42424328,18.8859006 8.060268,18.2498759 C8.06056298,18.2495809 8.06085784,18.2492858 8.0611526,18.2489906 L18.3196731,7.9746922 C18.9505124,7.34288268 18.9501191,6.31942463 18.3187946,5.68810005 L18.3187946,5.68810005 C17.68747,5.05677547 16.6640119,5.05638225 16.0322024,5.68722152 Z" fill="#000000" fill-rule="nonzero"/>
                                                <path d="M9.85714286,6.92857143 C9.85714286,8.54730513 8.5469533,9.85714286 6.93006028,9.85714286 C5.31316726,9.85714286 4,8.54730513 4,6.92857143 C4,5.30983773 5.31316726,4 6.93006028,4 C8.5469533,4 9.85714286,5.30983773 9.85714286,6.92857143 Z M20,17.0714286 C20,18.6901623 18.6898104,20 17.0729174,20 C15.4560244,20 14.1428571,18.6901623 14.1428571,17.0714286 C14.1428571,15.4497247 15.4560244,14.1428571 17.0729174,14.1428571 C18.6898104,14.1428571 20,15.4497247 20,17.0714286 Z" fill="#000000" opacity="0.3"/>
                                            </g>
                                        </svg>
                                    </span>
                                    <span class="kt-widget__desc">
                                        {{ KJLocalization::translate('Admin - Financieel', 'Btw', 'Btw') }}
                                    </span>
                                </span>
                            </a>

                            <a href="#settings" data-id="{{ $item->ID }}" class="kt-widget__item" data-toggle="tab" role="tab" aria-selected="false">
                                <span class="kt-widget__section">
                                    <span class="kt-widget__icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect opacity="0.200000003" x="0" y="0" width="24" height="24"/>
                                                <path d="M4.5,7 L9.5,7 C10.3284271,7 11,7.67157288 11,8.5 C11,9.32842712 10.3284271,10 9.5,10 L4.5,10 C3.67157288,10 3,9.32842712 3,8.5 C3,7.67157288 3.67157288,7 4.5,7 Z M13.5,15 L18.5,15 C19.3284271,15 20,15.6715729 20,16.5 C20,17.3284271 19.3284271,18 18.5,18 L13.5,18 C12.6715729,18 12,17.3284271 12,16.5 C12,15.6715729 12.6715729,15 13.5,15 Z" fill="#000000" opacity="0.3"/>
                                                <path d="M17,11 C15.3431458,11 14,9.65685425 14,8 C14,6.34314575 15.3431458,5 17,5 C18.6568542,5 20,6.34314575 20,8 C20,9.65685425 18.6568542,11 17,11 Z M6,19 C4.34314575,19 3,17.6568542 3,16 C3,14.3431458 4.34314575,13 6,13 C7.65685425,13 9,14.3431458 9,16 C9,17.6568542 7.65685425,19 6,19 Z" fill="#000000"/>
                                            </g>
                                        </svg>
                                    </span>
                                    <span class="kt-widget__desc">
                                        {{ KJLocalization::translate('Admin - Financieel', 'Instellingen & nummering', 'Instellingen & nummering') }}
                                    </span>
                                </span>
                            </a>

                            <a href="#payment_term" data-id="{{ $item->ID }}" class="kt-widget__item" data-toggle="tab" role="tab" aria-selected="false">
                                <span class="kt-widget__section">
                                    <span class="kt-widget__icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24"/>
                                                <path d="M4,4 L20,4 C21.1045695,4 22,4.8954305 22,6 L22,18 C22,19.1045695 21.1045695,20 20,20 L4,20 C2.8954305,20 2,19.1045695 2,18 L2,6 C2,4.8954305 2.8954305,4 4,4 Z" fill="#000000" opacity="0.3"/>
                                                <path d="M18.5,11 L5.5,11 C4.67157288,11 4,11.6715729 4,12.5 L4,13 L8.58578644,13 C8.85100293,13 9.10535684,13.1053568 9.29289322,13.2928932 L10.2928932,14.2928932 C10.7456461,14.7456461 11.3597108,15 12,15 C12.6402892,15 13.2543539,14.7456461 13.7071068,14.2928932 L14.7071068,13.2928932 C14.8946432,13.1053568 15.1489971,13 15.4142136,13 L20,13 L20,12.5 C20,11.6715729 19.3284271,11 18.5,11 Z" fill="#000000"/>
                                                <path d="M5.5,6 C4.67157288,6 4,6.67157288 4,7.5 L4,8 L20,8 L20,7.5 C20,6.67157288 19.3284271,6 18.5,6 L5.5,6 Z" fill="#000000"/>
                                            </g>
                                        </svg>
                                    </span>
                                    <span class="kt-widget__desc">
                                        {{ KJLocalization::translate('Admin - Financieel', 'Betalingsconditie', 'Betalingsconditie') }}
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
                            <div class="tab-pane active" id="ledgers" data-id="{{ $item->ID }}" role="tabpanel"></div>
                            <div class="tab-pane" id="vat" data-id="{{ $item->ID }}" role="tabpanel"></div>
                            <div class="tab-pane" id="settings" data-id="{{ $item->ID }}" role="tabpanel"></div>
                            <div class="tab-pane" id="payment_term" data-id="{{ $item->ID }}" role="tabpanel"></div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('page-resources')
    {!! Html::script('/assets/custom/js/admin/setting/finance/detail.js?v='.Cache::get('cache_version_number')) !!}
@endsection

