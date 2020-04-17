@extends('theme.demo1.main', ['title' => $item ? $item->title : KJLocalization::translate('Admin - Tasklists', 'Nieuwe takenlijst', 'Nieuwe takenlijst')])

@section('subheader')
    @component('breadcrums::main', [
        'rootURL'       => \KJ\Localization\libraries\LanguageUtils::getUrl('admin'),
        'parentTitle' => KJLocalization::translate('Admin - Menu', 'Takenlijsten', 'Takenlijsten'),
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
                'title' => KJLocalization::translate('Admin - Menu', 'Takenlijsten', 'Takenlijsten'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/tasklist'), '/')
            ],
            [
                'title' => $item ? $item->NAME : KJLocalization::translate('Admin - Tasklists', 'Nieuwe Takenlijst', 'Nieuwe Takenlijst'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/tasklist/detail/' . ($item->ID ?? -1)), '/')
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
                            <a href="#tasks" data-id="{{ $item->ID }}" class="kt-widget__item kt-widget__item--active" data-toggle="tab" role="tab">
                                <span class="kt-widget__section">
                                    <span class="kt-widget__icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24"/>
                                                <path d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z" fill="#000000" opacity="0.3"/>
                                                <path d="M10.875,15.75 C10.6354167,15.75 10.3958333,15.6541667 10.2041667,15.4625 L8.2875,13.5458333 C7.90416667,13.1625 7.90416667,12.5875 8.2875,12.2041667 C8.67083333,11.8208333 9.29375,11.8208333 9.62916667,12.2041667 L10.875,13.45 L14.0375,10.2875 C14.4208333,9.90416667 14.9958333,9.90416667 15.3791667,10.2875 C15.7625,10.6708333 15.7625,11.2458333 15.3791667,11.6291667 L11.5458333,15.4625 C11.3541667,15.6541667 11.1145833,15.75 10.875,15.75 Z" fill="#000000"/>
                                                <path d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z" fill="#000000"/>
                                            </g>
                                        </svg>
                                    </span>
                                    <span class="kt-widget__desc">
                                        {{ KJLocalization::translate('Admin - Dossiers', 'Taken', 'Taken') }}
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
                            <div class="tab-pane active" id="tasks" data-id="{{ $item->ID }}" data-type="{{ config('task_type.TYPE_TASKLIST') }}" role="tabpanel"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('page-resources')
    {!! Html::script('/assets/custom/js/admin/setting/tasklist/detail.js?v='.Cache::get('cache_version_number')) !!}
    {!! Html::script('/assets/custom/js/admin/tasks/shared.js?v='.Cache::get('cache_version_number')) !!}
@endsection

