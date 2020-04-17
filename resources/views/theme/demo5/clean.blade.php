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
        @include('base.demo5.meta')

        <!--begin::Fonts -->
        {!! HTML::script('/assets/theme/js/webfont.js?v='.Cache::get('cache_version_number')) !!}
        <script>
            WebFont.load({
                google: {
                    "families": ["Poppins:300,400,500,600,700"]
                },
                active: function() {
                    sessionStorage.fonts = true;
                }
            });
        </script>
        <!--end::Fonts -->

        <!--begin::Global Theme Styles(used by all pages) -->
        {!! Html::style('/assets/themes/demo5/plugins/global/plugins.bundle.css?v='.Cache::get('cache_version_number')) !!}
        {!! Html::style('/assets/themes/demo5/css/style.bundle.css?v='.Cache::get('cache_version_number')) !!}

        {!! Html::style('/assets/theme/css/demo5/style.css?v='.Cache::get('cache_version_number')) !!}
        {!! Html::style('/assets/custom/css/demo5/style.css?v='.Cache::get('cache_version_number')) !!}
        <!--end::Global Theme Styles -->

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
    <body class="kt-page--loading-enabled kt-page--loading kt-page--fixed kt-header--fixed kt-header--minimize-topbar kt-header-mobile--fixed kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-subheader--enabled kt-subheader--transparent kt-page--loading">

        <!-- begin:: Page -->
        <!-- begin:: Header Mobile -->
        <div id="kt_header_mobile" class="kt-header-mobile  kt-header-mobile--fixed ">
            <div class="kt-header-mobile__brand">
                <a class="kt-header-mobile__logo" href="/">
                    @if(file_exists('assets/custom/img/logos/demo5/logo.png'))
                        {!! Html::image('assets/custom/img/logos/demo5/logo.png', config('app.name'), ['style' => 'max-width: 140px; max-height: 50px']) !!}
                    @else
                        {!! Html::image('assets/theme/img/logos/demo5/logo.svg', config('app.name'), ['style' => 'max-width: 140px; max-height: 50px']) !!}
                    @endif
                </a>
            </div>
        </div>

        <!-- end:: Header Mobile -->
        <div class="kt-grid kt-grid--hor kt-grid--root">
            <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">
                <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper " id="kt_wrapper">

                    <!-- begin:: Header -->
                    <div id="kt_header" class="kt-header kt-grid__item  kt-header--fixed " data-ktheader-minimize="on">
                        <div class="kt-header__top">
                            <div class="kt-container">

                                <!-- begin:: Brand -->
                                <div class="kt-header__brand   kt-grid__item" id="kt_header_brand">
                                    <div class="kt-header__brand-logo">
                                        <a href="/">
                                            @if(file_exists('assets/custom/img/logos/demo5/logo.png'))
                                                {!! Html::image('assets/custom/img/logos/demo5/logo.png', config('app.name'), ['style' => 'max-width: 140px; max-height: 50px']) !!}
                                            @else
                                                {!! Html::image('assets/theme/img/logos/demo5/logo.svg', config('app.name'), ['style' => 'max-width: 140px; max-height: 50px']) !!}
                                            @endif
                                        </a>
                                    </div>
                                </div>
                                <!-- end:: Brand -->

                                <!-- begin:: Header Topbar -->
                                <div class="kt-header__topbar">
                                    @yield('topbar')
                                </div>
                                <!-- end:: Header Topbar -->
                            </div>
                        </div>
                    </div>

                    <!-- end:: Header -->
                    <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-grid--stretch">
                        <div class="kt-container kt-body  kt-grid kt-grid--ver" id="kt_body">
                            <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">

                                <!-- begin:: Content Head -->
                                @yield('subheader')
                                <!-- end:: Content Head -->

                                <!-- begin:: Content -->
                                <div class="kt-content kt-grid__item kt-grid__item--fluid">
                                    @yield('content')
                                </div>
                                <!-- end:: Content -->
                            </div>
                        </div>
                    </div>

                    <!-- begin:: Footer -->
                    @include('base.demo5.footer')
                    <!-- end:: Footer -->
                </div>
            </div>
        </div>
        <!-- end:: Page -->

        <!-- begin::Quick Panel -->
        @include('base.demo5.quick-panel')
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
                        "brand": "#3d94fb",
                        "light": "#ffffff",
                        "dark": "#282a3c",
                        "primary": "#5867dd",
                        "success": "#34bfa3",
                        "info": "#3d94fb",
                        "warning": "#ffb822",
                        "danger": "#fd27eb"
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
        {!! HTML::script('/assets/themes/demo5/plugins/global/plugins.bundle.js?v='.Cache::get('cache_version_number')) !!}
        {!! HTML::script('/assets/themes/demo5/js/scripts.bundle.js?v='.Cache::get('cache_version_number')) !!}
        <!--end::Global Theme Bundle -->

        <!--begin::Page Vendors(used by this page) -->
        @include('base.demo5.vendors')
        <!--end::Page Vendors -->

        <!--begin::Page Scripts(used by this page) -->
        @yield('page-resources')
        <!--end::Page Scripts -->
    </body>
    <!-- end::Body -->

</html>