<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">
    <!-- begin::Head -->
    <head>

        <meta charset="utf-8" />
        <title>{{ config('app.name') }} {{ isset($title) ? ' | ' . $title : '' }}</title>
        <meta name="description" content="">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="kj_cacheversion" content="{{ Cache::get('cache_version_number') }}">
        @include('base.demo1.meta')

        <!--begin::Fonts -->
        {!! HTML::script('/assets/theme/js/webfont.js?v='.Cache::get('cache_version_number')) !!}
        <script>
            WebFont.load({
                google: {
                    "families": ["Poppins:300,400,500,600,700", "Roboto:300,400,500,600,700"]
                },
                active: function() {
                    sessionStorage.fonts = true;
                }
            });
        </script>
        <!--end::Fonts -->

        <!--begin::Global Theme Styles(used by all pages) -->
        {!! Html::style('/assets/themes/demo1/plugins/global/plugins.bundle.css?v='.Cache::get('cache_version_number')) !!}
        {!! Html::style('/assets/themes/demo1/css/style.bundle.css?v='.Cache::get('cache_version_number')) !!}

        {!! Html::style('/assets/theme/css/demo1/style.css?v='.Cache::get('cache_version_number')) !!}
        {!! Html::style('/assets/custom/css/demo1/style.css?v='.Cache::get('cache_version_number')) !!}
        <!--end::Global Theme Styles -->

        <!--begin::Layout Skins(used by all pages) -->
        {!! Html::style('/assets/themes/demo1/css/skins/header/base/light.css?v='.Cache::get('cache_version_number')) !!}
        {!! Html::style('/assets/themes/demo1/css/skins/header/menu/light.css?v='.Cache::get('cache_version_number')) !!}
        {!! Html::style('/assets/themes/demo1/css/skins/brand/dark.css?v='.Cache::get('cache_version_number')) !!}
        {!! Html::style('/assets/themes/demo1/css/skins/aside/dark.css?v='.Cache::get('cache_version_number')) !!}
        <!--end::Layout Skins -->

        {{-- FullCalendar --}}
        {!! Html::style('/assets/themes/demo1/plugins/custom/fullcalendar/fullcalendar.bundle.css?v='.Cache::get('cache_version_number')) !!}
        {!! Html::style('/assets/custom/js/plugins/fullcalendar-4.3.1-plugins/timeline/main.min.css?v='.Cache::get('cache_version_number')) !!}
        {!! Html::style('/assets/custom/js/plugins/fullcalendar-4.3.1-plugins/resource-timeline/main.min.css?v='.Cache::get('cache_version_number')) !!}

        {{-- Material Design --}}
        {!! Html::style('/assets/custom/css/material_design/mdb.css?v='.Cache::get('cache_version_number')) !!}

        @if(file_exists('assets/custom/img/favicon/favicon.ico'))
            <link rel="shortcut icon" href="/assets/custom/img/favicon/favicon.ico" />
        @else
            <link rel="shortcut icon" href="/assets/theme/img/favicon/favicon.ico" />
        @endif

        @if(file_exists('assets/custom/img/favicon/apple-icon-180x180.png'))
            <link rel="apple-touch-icon" href="/assets/custom/img/favicon/apple-icon-180x180.png" />
        @else
            <link rel="apple-touch-icon" href="/assets/theme/img/favicon/apple-icon-180x180.png" />
        @endif
    </head>
    <!-- end::Head -->

    <!-- begin::Body -->
    <body class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--fixed kt-subheader--enabled kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-page--loading">

    <!-- begin:: Page -->

        <!-- begin:: Header Mobile -->
        <div id="kt_header_mobile" class="kt-header-mobile  kt-header-mobile--fixed ">
            <div class="kt-header-mobile__logo">
                <a href="/admin">
                    @if(file_exists('assets/custom/img/logos/demo1/logo.svg'))
                        {!! Html::image('assets/custom/img/logos/demo1/logo.svg', config('app.name'), ['style' => 'max-width: 140px; max-height: 50px']) !!}
                    @else
                        {!! Html::image('assets/theme/img/logos/demo1/logo.svg', config('app.name'), ['style' => 'max-width: 140px; max-height: 50px']) !!}
                    @endif
                </a>
            </div>
            <div class="kt-header-mobile__toolbar">
                @include('base.demo1.header-functions')
                <button class="kt-header-mobile__toggler kt-header-mobile__toggler--left" id="kt_aside_mobile_toggler"><span></span></button>
                {{--<button class="kt-header-mobile__topbar-toggler" id="kt_header_mobile_topbar_toggler"><i class="flaticon-more"></i></button>--}}
            </div>
        </div>
        <!-- end:: Header Mobile -->

        <div class="kt-grid kt-grid--hor kt-grid--root">
            <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">

                <!-- begin:: Aside -->
                <button class="kt-aside-close " id="kt_aside_close_btn"><i class="la la-close"></i></button>
                <div class="kt-aside  kt-aside--fixed  kt-grid__item kt-grid kt-grid--desktop kt-grid--hor-desktop" id="kt_aside">

                    <!-- begin:: Aside -->
                    <div class="kt-aside__brand kt-grid__item " id="kt_aside_brand">
                        <div class="kt-aside__brand-logo">
                            <a href="/admin">
                                @if(file_exists('assets/custom/img/logos/demo1/logo.svg'))
                                    {!! Html::image('assets/custom/img/logos/demo1/logo.svg', config('app.name'), ['style' => 'max-width: 140px; max-height: 50px']) !!}
                                @else
                                    {!! Html::image('assets/theme/img/logos/demo1/logo.svg', config('app.name'), ['style' => 'max-width: 140px; max-height: 50px']) !!}
                                @endif
                            </a>
                        </div>
                        <div class="kt-aside__brand-tools">
                            <button class="kt-aside__brand-aside-toggler" id="kt_aside_toggler">
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <polygon id="Shape" points="0 0 24 0 24 24 0 24" />
                                            <path d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z" id="Path-94" fill="#000000" fill-rule="nonzero" transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999) " />
                                            <path d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z" id="Path-94" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999) " />
                                        </g>
                                    </svg>
                                </span>
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <polygon id="Shape" points="0 0 24 0 24 24 0 24" />
                                            <path d="M12.2928955,6.70710318 C11.9023712,6.31657888 11.9023712,5.68341391 12.2928955,5.29288961 C12.6834198,4.90236532 13.3165848,4.90236532 13.7071091,5.29288961 L19.7071091,11.2928896 C20.085688,11.6714686 20.0989336,12.281055 19.7371564,12.675721 L14.2371564,18.675721 C13.863964,19.08284 13.2313966,19.1103429 12.8242777,18.7371505 C12.4171587,18.3639581 12.3896557,17.7313908 12.7628481,17.3242718 L17.6158645,12.0300721 L12.2928955,6.70710318 Z" id="Path-94" fill="#000000" fill-rule="nonzero" />
                                            <path d="M3.70710678,15.7071068 C3.31658249,16.0976311 2.68341751,16.0976311 2.29289322,15.7071068 C1.90236893,15.3165825 1.90236893,14.6834175 2.29289322,14.2928932 L8.29289322,8.29289322 C8.67147216,7.91431428 9.28105859,7.90106866 9.67572463,8.26284586 L15.6757246,13.7628459 C16.0828436,14.1360383 16.1103465,14.7686056 15.7371541,15.1757246 C15.3639617,15.5828436 14.7313944,15.6103465 14.3242754,15.2371541 L9.03007575,10.3841378 L3.70710678,15.7071068 Z" id="Path-94" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(9.000003, 11.999999) rotate(-270.000000) translate(-9.000003, -11.999999) " />
                                        </g>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>
                    <!-- end:: Aside -->

                    <!-- begin:: Aside Menu -->
                    <div class="kt-aside-menu-wrapper kt-grid__item kt-grid__item--fluid" id="kt_aside_menu_wrapper">
                        <div id="kt_aside_menu" class="kt-aside-menu " data-ktmenu-vertical="1" data-ktmenu-scroll="1" data-ktmenu-dropdown-timeout="500">
                            <ul class="kt-menu__nav ">
                                @if(env('APP_ENV') != 'production')
                                    <li class="kt-menu__section mt-0 app_env">
                                        <p>
                                            <strong>{{ strtoupper(KJLocalization::translate('Algemeen', 'Aanduiding omgeving', 'Geselecteerde omgeving')) }}</strong>: {{ strtoupper(env('APP_ENV')) }}<br/>
                                            <strong>{{ strtoupper(KJLocalization::translate('Algemeen', 'Debug mode', 'Debug mode')) }}</strong>: {{ strtoupper(env('APP_DEBUG') ? 'aan' : 'uit') }}
                                        </p>
                                    </li>
                                @endif

                                @include('base.demo1.navigation')
                            </ul>
                        </div>
                    </div>
                    <!-- end:: Aside Menu -->
                </div>
                <!-- end:: Aside -->

                <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">

                    <!-- begin:: Header -->
                    <div id="kt_header" class="kt-header kt-grid__item  kt-header--fixed ">

                        <!-- begin:: Header Menu -->
                        <button class="kt-header-menu-wrapper-close" id="kt_header_menu_mobile_close_btn"><i class="la la-close"></i></button>
                        <div class="kt-header-menu-wrapper" id="kt_header_menu_wrapper">
                            <div id="kt_header_menu" class="kt-header-menu kt-header-menu-mobile  kt-header-menu--layout-default ">
                                <ul class="kt-menu__nav ">
                                    @include('base.demo1.header-navigation')
                                    @yield('page-header-navigation')
                                </ul>
                            </div>
                        </div>
                        <!-- end:: Header Menu -->

                        <!-- begin:: Header Topbar -->
                        <div class="kt-header__topbar">
                            @include('base.demo1.header-functions')
                        </div>
                        <!-- end:: Header Topbar -->
                    </div>
                    <!-- end:: Header -->

                    <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
                        <!-- begin:: Content Head -->
                        @yield('subheader')
                        <!-- end:: Content Head -->

                        <!-- begin:: Content -->
                        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
                            @yield('content')
                        </div>
                        <!-- end:: Content -->
                    </div>

                    <!-- begin:: Footer -->
                    @include('base.demo1.footer')
                    <!-- end:: Footer -->
                </div>
            </div>
        </div>
        <!-- end:: Page -->

        <!-- begin::Quick Panel -->
        @include('base.demo1.quick-panel')
        <!-- end::Quick Panel -->

        <!-- begin::Scrolltop -->
        <div id="kt_scrolltop" class="kt-scrolltop">
            <i class="fa fa-arrow-up"></i>
        </div>
        <!-- end::Scrolltop -->

        <!-- begin::Global Config(global config for global JS sciprts) -->
        <script>
            var KTAppOptions = {
                "colors": {
                    "state": {
                        "brand": "#5d78ff",
                        "dark": "#282a3c",
                        "light": "#ffffff",
                        "primary": "#5867dd",
                        "success": "#34bfa3",
                        "info": "#36a3f7",
                        "warning": "#ffb822",
                        "danger": "#fd3995"
                    },
                    "base": {
                        "label": ["#c5cbe3", "#a1a8c3", "#3d4465", "#3e4466"],
                        "shape": ["#f0f3ff", "#d9dffa", "#afb4d4", "#646c9a"]
                    }
                }
            };
        </script>
        <!-- end::Global Config -->

        <!--begin::Global Theme Bundle(used by all pages) -->
        {!! HTML::script('/assets/themes/demo1/plugins/global/plugins.bundle.js?v='.Cache::get('cache_version_number')) !!}
        {!! HTML::script('/assets/themes/demo1/js/scripts.bundle.js?v='.Cache::get('cache_version_number')) !!}
        <!--end::Global Theme Bundle -->

        <!--begin::Page Vendors(used by this page) -->
        @include('base.demo1.vendors')
        <!--end::Page Vendors -->

        <!--begin::Page Scripts(used by this page) -->
        @yield('page-resources')
        <!--end::Page Scripts -->

    </body>
    <!-- end::Body -->

</html>