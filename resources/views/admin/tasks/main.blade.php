@extends('theme.demo1.main', ['title' => KJLocalization::translate('Admin - Menu', 'Taken', 'Taken')])

@section('subheader')
    @component('breadcrums::main', [
        'rootURL'       => \KJ\Localization\libraries\LanguageUtils::getUrl('admin'),
        'parentTitle' => KJLocalization::translate('Admin - Menu', 'Taken', 'Taken'),
        'items' => [
            [
                'title' => KJLocalization::translate('Algemeen', 'Home', 'Home'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin'), '/')
            ],
            [
                'title' => KJLocalization::translate('Admin - Menu', 'Taken', 'Taken'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/tasks'), '/')
            ]
        ]
    ])
    @slot('actionbar')
        <a href="javascript:;" id="addMap" class="btn btn-success btn-sm">
            <i class="la la-plus"></i>
            {{ KJLocalization::translate('Admin - Taken', 'Map aanmaken', 'Map aanmaken')}}
        </a>
    @endslot
    @endcomponent
@endsection

@section('content')
    <div class="row" id="containment">
        @component('portlet::main', ['notitle' => true, 'colsize' => 3])
            @php($currentTab = \KJ\Core\libraries\SessionUtils::getSession('ADM_TASK', 'CURRENT_TAB', config('task_type.TYPE_TODAY')))

            <div class="kt-widget kt-widget--user-profile-1 pb-0">
                <div class="kt-widget__body">
                    <div class="kt-widget__items nav" role="tablist">
                        <a href="#today_tasks" class="kt-widget__item {{ ($currentTab == config('task_type.TYPE_TODAY')) ? 'kt-widget__item--active' : '' }}" data-toggle="tab" role="tab">
                            <span class="kt-widget__section">
                                <span class="kt-widget__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24"/>
                                            <path d="M12,21 C7.581722,21 4,17.418278 4,13 C4,8.581722 7.581722,5 12,5 C16.418278,5 20,8.581722 20,13 C20,17.418278 16.418278,21 12,21 Z" fill="#000000" opacity="0.3"/>
                                            <path d="M13,5.06189375 C12.6724058,5.02104333 12.3386603,5 12,5 C11.6613397,5 11.3275942,5.02104333 11,5.06189375 L11,4 L10,4 C9.44771525,4 9,3.55228475 9,3 C9,2.44771525 9.44771525,2 10,2 L14,2 C14.5522847,2 15,2.44771525 15,3 C15,3.55228475 14.5522847,4 14,4 L13,4 L13,5.06189375 Z" fill="#000000"/>
                                            <path d="M16.7099142,6.53272645 L17.5355339,5.70710678 C17.9260582,5.31658249 18.5592232,5.31658249 18.9497475,5.70710678 C19.3402718,6.09763107 19.3402718,6.73079605 18.9497475,7.12132034 L18.1671361,7.90393167 C17.7407802,7.38854954 17.251061,6.92750259 16.7099142,6.53272645 Z" fill="#000000"/>
                                            <path d="M11.9630156,7.5 L12.0369844,7.5 C12.2982526,7.5 12.5154733,7.70115317 12.5355117,7.96165175 L12.9585886,13.4616518 C12.9797677,13.7369807 12.7737386,13.9773481 12.4984096,13.9985272 C12.4856504,13.9995087 12.4728582,14 12.4600614,14 L11.5399386,14 C11.2637963,14 11.0399386,13.7761424 11.0399386,13.5 C11.0399386,13.4872031 11.0404299,13.4744109 11.0414114,13.4616518 L11.4644883,7.96165175 C11.4845267,7.70115317 11.7017474,7.5 11.9630156,7.5 Z" fill="#000000"/>
                                        </g>
                                    </svg>
                                </span>
                                <span class="kt-widget__desc">
                                    {{ KJLocalization::translate('Admin - Taken', 'Vandaag', 'Vandaag') }}
                                </span>
                            </span>
                            {{--<span class="kt-nav__link-badge">--}}
                            {{--<span class="kt-badge kt-badge--unified-success kt-badge--md kt-badge--rounded kt-badge--boldest">--}}
                            {{--TODO::3--}}
                            {{--</span>--}}
                            {{--</span>--}}
                        </a>

                        <a href="#this_week_tasks" class="kt-widget__item {{ ($currentTab == config('task_type.TYPE_WEEK')) ? 'kt-widget__item--active' : '' }}" data-toggle="tab" role="tab" aria-selected="false">
                            <span class="kt-widget__section">
                                <span class="kt-widget__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24"/>
                                            <path d="M12,21 C7.581722,21 4,17.418278 4,13 C4,8.581722 7.581722,5 12,5 C16.418278,5 20,8.581722 20,13 C20,17.418278 16.418278,21 12,21 Z" fill="#000000" opacity="0.3"/>
                                            <path d="M13,5.06189375 C12.6724058,5.02104333 12.3386603,5 12,5 C11.6613397,5 11.3275942,5.02104333 11,5.06189375 L11,4 L10,4 C9.44771525,4 9,3.55228475 9,3 C9,2.44771525 9.44771525,2 10,2 L14,2 C14.5522847,2 15,2.44771525 15,3 C15,3.55228475 14.5522847,4 14,4 L13,4 L13,5.06189375 Z" fill="#000000"/>
                                            <path d="M16.7099142,6.53272645 L17.5355339,5.70710678 C17.9260582,5.31658249 18.5592232,5.31658249 18.9497475,5.70710678 C19.3402718,6.09763107 19.3402718,6.73079605 18.9497475,7.12132034 L18.1671361,7.90393167 C17.7407802,7.38854954 17.251061,6.92750259 16.7099142,6.53272645 Z" fill="#000000"/>
                                            <path d="M11.9630156,7.5 L12.0369844,7.5 C12.2982526,7.5 12.5154733,7.70115317 12.5355117,7.96165175 L12.9585886,13.4616518 C12.9797677,13.7369807 12.7737386,13.9773481 12.4984096,13.9985272 C12.4856504,13.9995087 12.4728582,14 12.4600614,14 L11.5399386,14 C11.2637963,14 11.0399386,13.7761424 11.0399386,13.5 C11.0399386,13.4872031 11.0404299,13.4744109 11.0414114,13.4616518 L11.4644883,7.96165175 C11.4845267,7.70115317 11.7017474,7.5 11.9630156,7.5 Z" fill="#000000"/>
                                        </g>
                                    </svg>
                                </span>
                                <span class="kt-widget__desc">
                                    {{ KJLocalization::translate('Admin - Taken', 'Deze week', 'Deze week') }}
                                </span>
                            </span>
                            {{--<span class="kt-nav__link-badge">--}}
                                {{--<span class="kt-badge kt-badge--unified-success kt-badge--md kt-badge--rounded kt-badge--boldest">--}}
                                    {{--TODO::3--}}
                                {{--</span>--}}
                            {{--</span>--}}
                        </a>

                        <a href="#this_month_tasks" class="kt-widget__item {{ ($currentTab == config('task_type.TYPE_MONTH')) ? 'kt-widget__item--active' : '' }}" data-toggle="tab" role="tab" aria-selected="false">
                            <span class="kt-widget__section">
                                <span class="kt-widget__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24"/>
                                            <path d="M12,21 C7.581722,21 4,17.418278 4,13 C4,8.581722 7.581722,5 12,5 C16.418278,5 20,8.581722 20,13 C20,17.418278 16.418278,21 12,21 Z" fill="#000000" opacity="0.3"/>
                                            <path d="M13,5.06189375 C12.6724058,5.02104333 12.3386603,5 12,5 C11.6613397,5 11.3275942,5.02104333 11,5.06189375 L11,4 L10,4 C9.44771525,4 9,3.55228475 9,3 C9,2.44771525 9.44771525,2 10,2 L14,2 C14.5522847,2 15,2.44771525 15,3 C15,3.55228475 14.5522847,4 14,4 L13,4 L13,5.06189375 Z" fill="#000000"/>
                                            <path d="M16.7099142,6.53272645 L17.5355339,5.70710678 C17.9260582,5.31658249 18.5592232,5.31658249 18.9497475,5.70710678 C19.3402718,6.09763107 19.3402718,6.73079605 18.9497475,7.12132034 L18.1671361,7.90393167 C17.7407802,7.38854954 17.251061,6.92750259 16.7099142,6.53272645 Z" fill="#000000"/>
                                            <path d="M11.9630156,7.5 L12.0369844,7.5 C12.2982526,7.5 12.5154733,7.70115317 12.5355117,7.96165175 L12.9585886,13.4616518 C12.9797677,13.7369807 12.7737386,13.9773481 12.4984096,13.9985272 C12.4856504,13.9995087 12.4728582,14 12.4600614,14 L11.5399386,14 C11.2637963,14 11.0399386,13.7761424 11.0399386,13.5 C11.0399386,13.4872031 11.0404299,13.4744109 11.0414114,13.4616518 L11.4644883,7.96165175 C11.4845267,7.70115317 11.7017474,7.5 11.9630156,7.5 Z" fill="#000000"/>
                                        </g>
                                    </svg>
                                </span>
                                <span class="kt-widget__desc">
                                    {{ KJLocalization::translate('Admin - Taken', 'Deze maand', 'Deze maand') }}
                                </span>
                            </span>
                            {{--<span class="kt-nav__link-badge">--}}
                                {{--<span class="kt-badge kt-badge--unified-success kt-badge--md kt-badge--rounded kt-badge--boldest">--}}
                                    {{--TODO::3--}}
                                {{--</span>--}}
                            {{--</span>--}}
                        </a>

                        <a href="#subscribed_tasks" class="kt-widget__item {{ ($currentTab == config('task_type.TYPE_SUBSCRIBED')) ? 'kt-widget__item--active' : '' }}" data-toggle="tab" role="tab" aria-selected="false">
                            <span class="kt-widget__section">
                                <span class="kt-widget__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <polygon points="0 0 24 0 24 24 0 24"/>
                                            <path d="M12,18 L7.91561963,20.1472858 C7.42677504,20.4042866 6.82214789,20.2163401 6.56514708,19.7274955 C6.46280801,19.5328351 6.42749334,19.309867 6.46467018,19.0931094 L7.24471742,14.545085 L3.94038429,11.3241562 C3.54490071,10.938655 3.5368084,10.3055417 3.92230962,9.91005817 C4.07581822,9.75257453 4.27696063,9.65008735 4.49459766,9.61846284 L9.06107374,8.95491503 L11.1032639,4.81698575 C11.3476862,4.32173209 11.9473121,4.11839309 12.4425657,4.36281539 C12.6397783,4.46014562 12.7994058,4.61977315 12.8967361,4.81698575 L14.9389263,8.95491503 L19.5054023,9.61846284 C20.0519472,9.69788046 20.4306287,10.2053233 20.351211,10.7518682 C20.3195865,10.9695052 20.2170993,11.1706476 20.0596157,11.3241562 L16.7552826,14.545085 L17.5353298,19.0931094 C17.6286908,19.6374458 17.263103,20.1544017 16.7187666,20.2477627 C16.5020089,20.2849396 16.2790408,20.2496249 16.0843804,20.1472858 L12,18 Z" fill="#000000"/>
                                        </g>
                                    </svg>
                                </span>
                                <span class="kt-widget__desc">
                                    {{ KJLocalization::translate('Admin - Taken', 'Geabonneerde taken', 'Geabonneerde taken') }}
                                </span>
                            </span>
                            {{--<span class="kt-nav__link-badge">--}}
                                {{--<span class="kt-badge kt-badge--unified-success kt-badge--md kt-badge--rounded kt-badge--boldest">--}}
                                    {{--TODO::3--}}
                                {{--</span>--}}
                            {{--</span>--}}
                        </a>

                        <a href="#open_tasks" class="kt-widget__item {{ ($currentTab == config('task_type.TYPE_OPEN')) ? 'kt-widget__item--active' : '' }}" data-toggle="tab" role="tab" aria-selected="false">
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
                                    {{ KJLocalization::translate('Admin - Taken', 'Openstaande taken', 'Openstaande taken') }}
                                </span>
                            </span>
                            {{--<span class="kt-nav__link-badge">--}}
                                {{--<span class="kt-badge kt-badge--unified-success kt-badge--md kt-badge--rounded kt-badge--boldest">--}}
                                    {{--TODO::3--}}
                                {{--</span>--}}
                            {{--</span>--}}
                        </a>

                        <a href="#all_tasks" class="kt-widget__item {{ ($currentTab == config('task_type.TYPE_ALL')) ? 'kt-widget__item--active' : '' }}" data-toggle="tab" role="tab" aria-selected="false">
                            <span class="kt-widget__section">
                                <span class="kt-widget__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24"/>
                                            <path d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z" fill="#000000" opacity="0.3"/>
                                            <path d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z" fill="#000000"/>
                                            <rect fill="#000000" opacity="0.3" x="10" y="9" width="7" height="2" rx="1"/>
                                            <rect fill="#000000" opacity="0.3" x="7" y="9" width="2" height="2" rx="1"/>
                                            <rect fill="#000000" opacity="0.3" x="7" y="13" width="2" height="2" rx="1"/>
                                            <rect fill="#000000" opacity="0.3" x="10" y="13" width="7" height="2" rx="1"/>
                                            <rect fill="#000000" opacity="0.3" x="7" y="17" width="2" height="2" rx="1"/>
                                            <rect fill="#000000" opacity="0.3" x="10" y="17" width="7" height="2" rx="1"/>
                                        </g>
                                    </svg>
                                </span>
                                <span class="kt-widget__desc">
                                    {{ KJLocalization::translate('Admin - Taken', 'Alle taken', 'Alle taken') }}
                                </span>
                            </span>
                            {{--<span class="kt-nav__link-badge">--}}
                                {{--<span class="kt-badge kt-badge--unified-success kt-badge--md kt-badge--rounded kt-badge--boldest">--}}
                                    {{--TODO::3--}}
                                {{--</span>--}}
                            {{--</span>--}}
                        </a>

                        <a href="#closed_tasks" class="kt-widget__item {{ ($currentTab == config('task_type.TYPE_DONE')) ? 'kt-widget__item--active' : '' }}" data-toggle="tab" role="tab" aria-selected="false">
                            <span class="kt-widget__section">
                                <span class="kt-widget__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <polygon points="0 0 24 0 24 24 0 24"/>
                                            <path d="M5.85714286,2 L13.7364114,2 C14.0910962,2 14.4343066,2.12568431 14.7051108,2.35473959 L19.4686994,6.3839416 C19.8056532,6.66894833 20,7.08787823 20,7.52920201 L20,20.0833333 C20,21.8738751 19.9795521,22 18.1428571,22 L5.85714286,22 C4.02044787,22 4,21.8738751 4,20.0833333 L4,3.91666667 C4,2.12612489 4.02044787,2 5.85714286,2 Z M10.875,15.75 C11.1145833,15.75 11.3541667,15.6541667 11.5458333,15.4625 L15.3791667,11.6291667 C15.7625,11.2458333 15.7625,10.6708333 15.3791667,10.2875 C14.9958333,9.90416667 14.4208333,9.90416667 14.0375,10.2875 L10.875,13.45 L9.62916667,12.2041667 C9.29375,11.8208333 8.67083333,11.8208333 8.2875,12.2041667 C7.90416667,12.5875 7.90416667,13.1625 8.2875,13.5458333 L10.2041667,15.4625 C10.3958333,15.6541667 10.6354167,15.75 10.875,15.75 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                            <path d="M10.875,15.75 C10.6354167,15.75 10.3958333,15.6541667 10.2041667,15.4625 L8.2875,13.5458333 C7.90416667,13.1625 7.90416667,12.5875 8.2875,12.2041667 C8.67083333,11.8208333 9.29375,11.8208333 9.62916667,12.2041667 L10.875,13.45 L14.0375,10.2875 C14.4208333,9.90416667 14.9958333,9.90416667 15.3791667,10.2875 C15.7625,10.6708333 15.7625,11.2458333 15.3791667,11.6291667 L11.5458333,15.4625 C11.3541667,15.6541667 11.1145833,15.75 10.875,15.75 Z" fill="#000000"/>
                                        </g>
                                    </svg>
                                </span>
                                <span class="kt-widget__desc">
                                    {{ KJLocalization::translate('Admin - Taken', 'Afgeronde taken', 'Afgeronde taken') }}
                                </span>
                            </span>
                            {{--<span class="kt-nav__link-badge">--}}
                                {{--<span class="kt-badge kt-badge--unified-success kt-badge--md kt-badge--rounded kt-badge--boldest">--}}
                                    {{--TODO::3--}}
                                {{--</span>--}}
                            {{--</span>--}}
                        </a>
                        @foreach($customMaps as $customMap)
                            <div class="row">
                                <div class="col-8">
                                    <a href="#{{$customMap->nameUnderScored()}}" class="kt-widget__item {{ ($currentTab == $customMap->NAME) ? 'kt-widget__item--active' : '' }}" data-toggle="tab" role="tab">
                                        <span class="kt-widget__section" style="width:100%;">
                                            <span class="kt-widget__icon">
                                               <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24"/>
                                                        <path d="M3.5,21 L20.5,21 C21.3284271,21 22,20.3284271 22,19.5 L22,8.5 C22,7.67157288 21.3284271,7 20.5,7 L10,7 L7.43933983,4.43933983 C7.15803526,4.15803526 6.77650439,4 6.37867966,4 L3.5,4 C2.67157288,4 2,4.67157288 2,5.5 L2,19.5 C2,20.3284271 2.67157288,21 3.5,21 Z" fill="#000000" opacity="0.3"/>
                                                        <path d="M11.9999651,17.2276651 L9.80187391,18.4352848 C9.53879239,18.5798204 9.21340017,18.4741205 9.07509004,18.1991974 C9.02001422,18.0897216 9.00100892,17.9643258 9.02101638,17.8424227 L9.44081443,15.2846431 L7.66252134,13.4732136 C7.44968392,13.2564102 7.44532889,12.9003514 7.65279409,12.677934 C7.73540782,12.5893662 7.84365664,12.5317281 7.96078237,12.5139426 L10.418323,12.1407676 L11.5173686,9.81362288 C11.6489093,9.53509542 11.97161,9.42073887 12.2381407,9.5582004 C12.3442746,9.6129383 12.4301813,9.70271178 12.4825615,9.81362288 L13.5816071,12.1407676 L16.0391477,12.5139426 C16.3332818,12.5586066 16.5370768,12.8439892 16.4943366,13.1513625 C16.4773173,13.2737601 16.4221618,13.3868813 16.3374088,13.4732136 L14.5591157,15.2846431 L14.9789137,17.8424227 C15.0291578,18.148554 14.8324094,18.4392867 14.5394638,18.4917923 C14.4228114,18.5127004 14.3028166,18.4928396 14.1980562,18.4352848 L11.9999651,17.2276651 Z" fill="#000000" opacity="0.3"/>
                                                    </g>
                                                </svg>
                                            </span>
                                            <span class="kt-widget__desc">
                                                {{$customMap->NAME}}

                                            </span>
                                        </span>
                                    </a>
                                </div>
                                <div class="col-2" style="padding-right: 5px; padding-left: 10px;">
                                    <a href="#" class="kt-widget__item" data-toggle="tab" role="tab">
                                        <span class="kt-widget__section" style="width:100%;">
                                            <span class="kt-widget__icon editCustomMap" data-id="{{$customMap->ID}}">
                                                 <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24"/>
                                                        <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "/>
                                                        <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"/>
                                                    </g>
                                                </svg>
                                            </span>
                                        </span>
                                    </a>
                                </div>
                                <div class="col-2" style="padding-right: 5px ">
                                    <a href="#" class="kt-widget__item " data-toggle="tab" role="tab">
                                        <span class="kt-widget__section" style="width:100%;">
                                            <span class="kt-widget__icon deleteCustomMap" data-id="{{$customMap->ID}}">
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24"/>
                                                        <path d="M3.5,21 L20.5,21 C21.3284271,21 22,20.3284271 22,19.5 L22,8.5 C22,7.67157288 21.3284271,7 20.5,7 L10,7 L7.43933983,4.43933983 C7.15803526,4.15803526 6.77650439,4 6.37867966,4 L3.5,4 C2.67157288,4 2,4.67157288 2,5.5 L2,19.5 C2,20.3284271 2.67157288,21 3.5,21 Z" fill="#000000" opacity="0.3"/>
                                                        <path d="M10.5857864,14 L9.17157288,12.5857864 C8.78104858,12.1952621 8.78104858,11.5620972 9.17157288,11.1715729 C9.56209717,10.7810486 10.1952621,10.7810486 10.5857864,11.1715729 L12,12.5857864 L13.4142136,11.1715729 C13.8047379,10.7810486 14.4379028,10.7810486 14.8284271,11.1715729 C15.2189514,11.5620972 15.2189514,12.1952621 14.8284271,12.5857864 L13.4142136,14 L14.8284271,15.4142136 C15.2189514,15.8047379 15.2189514,16.4379028 14.8284271,16.8284271 C14.4379028,17.2189514 13.8047379,17.2189514 13.4142136,16.8284271 L12,15.4142136 L10.5857864,16.8284271 C10.1952621,17.2189514 9.56209717,17.2189514 9.17157288,16.8284271 C8.78104858,16.4379028 8.78104858,15.8047379 9.17157288,15.4142136 L10.5857864,14 Z" fill="#000000"/>
                                                    </g>
                                                </svg>
                                            </span>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endcomponent

        <div class="col-lg-9">
            @if(Auth::guard()->user()->hasPermission(config('permission.TAKEN_INZIEN')))
                <div class="kt-portlet kt-portlet--mobile" id="ASSIGNEE_FILTER">
                    <div class="kt-portlet__body">
                        <div class="kt-form kt-form--label-right">
                            <div class="row align-items-center">
                                <div class="col-lg-12">
                                    <div class="row align-items-center">
                                        <div class="col-4">
                                            <div class="kt-form__group kt-form__group--inline">
                                                <div class="kt-form__label">
                                                    {{ Form::label('ADM_FILTER_TASK_ASSIGNEE', KJLocalization::translate('Algemeen', 'Toegewezen aan', 'Toegewezen aan'). ':', ['style' => 'width: 120px;']) }}
                                                </div>
                                                <div class="kt-form__control">
                                                    {{ Form::select(
                                                        'ADM_FILTER_TASK_ASSIGNEE',
                                                        $users,
                                                        \KJ\Core\libraries\SessionUtils::getSession('ADM_TASK', 'ADM_FILTER_TASK_ASSIGNEE', auth()->user()->ID),
                                                        [
                                                            'class' => 'form-control filter kt-bootstrap-select hasSessionState',
                                                            'id'            => 'ADM_FILTER_TASK_ASSIGNEE',
                                                            'data-module'   => 'ADM_TASK',
                                                            'data-key'      => 'ADM_FILTER_TASK_ASSIGNEE'
                                                        ]
                                                    ) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="kt-form__group kt-form__group--inline">
                                                <div class="kt-form__label">
                                                    {{ Form::label('ADM_FILTER_TASK_FILTERS', KJLocalization::translate('Admin - Taken', 'Categorie', 'Categorie'). ':') }}
                                                </div>
                                                <div class="kt-form__control">
                                                    {{ Form::select(
                                                        'ADM_FILTER_TASK_FILTERS',
                                                        $filters,
                                                        \KJ\Core\libraries\SessionUtils::getSession('ADM_TASK', 'ADM_FILTER_TASK_FILTERS', 1),
                                                        [
                                                            'class' => 'form-control filter kt-bootstrap-select hasSessionState',
                                                            'id'            => 'ADM_FILTER_TASK_FILTERS',
                                                            'data-module'   => 'ADM_TASK',
                                                            'data-key'      => 'ADM_FILTER_TASK_FILTERS'
                                                        ]
                                                    ) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 filterDeadline">
                                            <div class="kt-form__group kt-form__group--inline">
                                                <div class="kt-form__label">
                                                    {{ Form::label('ADM_FILTER_LBL_TASK_DATE', KJLocalization::translate('Admin - Taken', 'Deadline', 'Deadline'). ':') }}
                                                </div>
                                                <div class="kt-form__control">
                                                    {{ KJField::daterangepicker(
                                                        'ADM_FILTER_TASK_DATE_GROUP',
                                                        'ADM_FILTER_TASK_DATE',
                                                        '',
                                                        array(
                                                            'class' => 'form-control kjdaterangepicker-picker hasSessionState',
                                                            'data-start-date' => \KJ\Core\libraries\SessionUtils::getSession('ADM_TASK', 'ADM_FILTER_TASK_DATE_startDate', date(\KJ\Localization\libraries\LanguageUtils::getDateFormat(), strtotime('today'))),
                                                            'data-end-date' => \KJ\Core\libraries\SessionUtils::getSession('ADM_TASK', 'ADM_FILTER_TASK_DATE_endDate', date(\KJ\Localization\libraries\LanguageUtils::getDateFormat(), strtotime('+6 weeks'))),
                                                            'data-module'   => 'ADM_TASK',
                                                            'data-key'      => 'ADM_FILTER_TASK_DATE',
                                                            'data-locale-format' => \KJ\Localization\libraries\LanguageUtils::getJSDateFormat()
                                                        )
                                                    ) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="kt-portlet kt-portlet--mobile" id="detailScreenContainer">
                <div class="kt-portlet__body kt-portlet__body--fit">
                    <div class="tab-content">
                        <div class="tab-pane {{ ($currentTab == config('task_type.TYPE_TODAY')) ? 'active' : '' }}" id="today_tasks" data-type="{{ config('task_type.TYPE_TODAY') }}" role="tabpanel"></div>
                        <div class="tab-pane {{ ($currentTab == config('task_type.TYPE_WEEK')) ? 'active' : '' }}" id="this_week_tasks" data-type="{{ config('task_type.TYPE_WEEK') }}" role="tabpanel"></div>
                        <div class="tab-pane {{ ($currentTab == config('task_type.TYPE_MONTH')) ? 'active' : '' }}" id="this_month_tasks" data-type="{{ config('task_type.TYPE_MONTH') }}" role="tabpanel"></div>
                        <div class="tab-pane {{ ($currentTab == config('task_type.TYPE_SUBSCRIBED')) ? 'active' : '' }}" id="subscribed_tasks" data-type="{{ config('task_type.TYPE_SUBSCRIBED') }}" role="tabpanel"></div>
                        <div class="tab-pane {{ ($currentTab == config('task_type.TYPE_OPEN')) ? 'active' : '' }}" id="open_tasks" data-type="{{ config('task_type.TYPE_OPEN') }}" role="tabpanel"></div>
                        <div class="tab-pane {{ ($currentTab == config('task_type.TYPE_ALL')) ? 'active' : '' }}" id="all_tasks" data-type="{{ config('task_type.TYPE_ALL') }}" role="tabpanel"></div>
                        <div class="tab-pane {{ ($currentTab == config('task_type.TYPE_DONE')) ? 'active' : '' }}" id="closed_tasks" data-type="{{ config('task_type.TYPE_DONE') }}" role="tabpanel"></div>
                        @foreach($customMaps as $customMap)
                            <div class="tab-pane {{ ($currentTab == $customMap->NAME) ? 'active' : '' }}" id="{{$customMap->nameUnderScored()}}" data-type="{{$customMap->NAME}}" role="tabpanel"></div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-resources')
    {!! Html::script('/assets/custom/js/admin/tasks/main.js?v='.Cache::get('cache_version_number')) !!}
    {!! Html::script('/assets/custom/js/admin/tasks/shared.js?v='.Cache::get('cache_version_number')) !!}
    {!! Html::script('/assets/themes/demo1/plugins/custom/jquery-ui/jquery-ui.bundle.js?v='.Cache::get('cache_version_number')) !!}
@endsection