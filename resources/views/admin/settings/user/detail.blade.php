@extends('theme.demo1.main', ['title' => $item ? $item->title : KJLocalization::translate('Admin - Werknemers', 'Nieuwe werknemer', 'Nieuwe werknemer')])

@section('subheader')
    @component('breadcrums::main', [
        'rootURL'       => \KJ\Localization\libraries\LanguageUtils::getUrl('admin'),
        'parentTitle' => KJLocalization::translate('Admin - Menu', 'Werknemers', 'Werknemers'),
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
                'title' => KJLocalization::translate('Admin - Menu', 'Werknemers', 'Werknemers'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/user'), '/')
            ],
            [
                'title' => $item ? $item->title : KJLocalization::translate('Admin - Werknemers', 'Nieuwe werknemer', 'Nieuwe werknemer'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/user/detail/' . ($item->ID ?? -1)), '/')
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
            @component('portlet::main', ['notitle' => true, 'colsize' => 3, 'portletClass' => 'kt-hidden'])
                <div class="kt-widget kt-widget--user-profile-1 pb-0">
                    <div class="kt-widget__body">
                        <div class="kt-widget__items nav" role="tablist">
                            <a href="#permissions" data-id="{{ $item->ID }}" class="kt-widget__item kt-widget__item--active" data-toggle="tab" role="tab" aria-selected="true">
                                <span class="kt-widget__section">
                                    <span class="kt-widget__icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <mask fill="white">
                                                    <use xlink:href="#path-1"/>
                                                </mask>
                                                <g/>
                                                <path d="M7,10 L7,8 C7,5.23857625 9.23857625,3 12,3 C14.7614237,3 17,5.23857625 17,8 L17,10 L18,10 C19.1045695,10 20,10.8954305 20,12 L20,18 C20,19.1045695 19.1045695,20 18,20 L6,20 C4.8954305,20 4,19.1045695 4,18 L4,12 C4,10.8954305 4.8954305,10 6,10 L7,10 Z M12,5 C10.3431458,5 9,6.34314575 9,8 L9,10 L15,10 L15,8 C15,6.34314575 13.6568542,5 12,5 Z" fill="#000000"/>
                                            </g>
                                        </svg>
                                    </span>
                                    <span class="kt-widget__desc">
                                        {{ KJLocalization::translate('Admin - Werknemers', 'Rollen en rechten', 'Rollen en rechten') }}
                                    </span>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            @endcomponent

            <div class="col-lg-12">
                <div class="kt-portlet kt-portlet--mobile kt-portlet--height-fluid" id="detailScreenContainer">
                    <div class="kt-portlet__body kt-portlet__body--fit">
                        <div class="tab-content">
                            <div class="tab-pane active" id="permissions" data-id="{{ $item->ID }}" role="tabpanel"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('page-resources')
    {!! Html::script('/assets/custom/js/admin/setting/user/detail.js?v='.Cache::get('cache_version_number')) !!}
@endsection