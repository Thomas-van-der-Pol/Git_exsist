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

        {!! Html::style('/assets/themes/demo1/css/pages/login/login-1.css?v='.Cache::get('cache_version_number')) !!}

        <!--begin::Global Theme Styles(used by all pages) -->
        {!! Html::style('/assets/themes/demo1/plugins/global/plugins.bundle.css?v='.Cache::get('cache_version_number')) !!}
        {!! Html::style('/assets/themes/demo1/css/style.bundle.css?v='.Cache::get('cache_version_number')) !!}

        {!! Html::style('/assets/theme/css/demo1/login.css?v='.Cache::get('cache_version_number')) !!}
        {!! Html::style('/assets/custom/css/demo1/login.css?v='.Cache::get('cache_version_number')) !!}
        <!--end::Global Theme Styles -->

        <!--begin::Layout Skins(used by all pages) -->
        {!! Html::style('/assets/themes/demo1/css/skins/header/base/light.css?v='.Cache::get('cache_version_number')) !!}
        {!! Html::style('/assets/themes/demo1/css/skins/header/menu/light.css?v='.Cache::get('cache_version_number')) !!}
        {!! Html::style('/assets/themes/demo1/css/skins/brand/dark.css?v='.Cache::get('cache_version_number')) !!}
        {!! Html::style('/assets/themes/demo1/css/skins/aside/dark.css?v='.Cache::get('cache_version_number')) !!}
        <!--end::Layout Skins -->

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
    <div class="kt-grid kt-grid--ver kt-grid--root">
        <div class="kt-grid kt-grid--hor kt-grid--root  kt-login kt-login--v1" id="kt_login">
            <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--desktop kt-grid--ver-desktop kt-grid--hor-tablet-and-mobile">

                <!--begin::Aside-->
                <div class="kt-grid__item kt-grid__item--order-tablet-and-mobile-2 kt-grid kt-grid--hor kt-login__aside" style="background-image: url({{ ( (file_exists('assets/custom/img/media/bg/bg.jpg')) ? asset('assets/custom/img/media/bg/bg.jpg') : asset('assets/theme/img/media/bg/bg-4.jpg') ) }});">
                    <div class="kt-grid__item">
                        <a href="#" class="kt-login__logo">
                            @if(file_exists('assets/custom/img/logos/demo1/logo-login.png'))
                                {!! Html::image('assets/custom/img/logos/demo1/logo-login.png', config('app.name'), ['style' => 'max-width: 140px; max-height: 70px']) !!}
                            @else
                                {!! Html::image('assets/theme/img/logos/demo1/logo-login.svg', config('app.name'), ['style' => 'max-width: 140px; max-height: 70px']) !!}
                            @endif
                        </a>
                    </div>
                    <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver">
                        <div class="kt-grid__item kt-grid__item--middle">
                            {{--<h3 class="kt-login__title">{{ config('app.title') }}</h3>--}}
                            {{--<h4 class="kt-login__subtitle">{{ config('app.subtitle') }}</h4>--}}
                        </div>
                    </div>
                    <div class="kt-grid__item">
                        <div class="kt-login__info">
                            <div class="kt-login__copyright">
                                &copy; {{ config('app.copyright') }} {{ date('Y') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!--begin::Aside-->

                <!--begin::Content-->
                <div class="kt-grid__item kt-grid__item--fluid  kt-grid__item--order-tablet-and-mobile-1  kt-login__wrapper">
                    @yield('content')
                </div>

                <!--end::Content-->
            </div>
        </div>
    </div>

    <!-- end:: Page -->

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
        {!! HTML::script('/assets/theme/js/auth/login.js?v='.Cache::get('cache_version_number')) !!}
        @yield('page-resources')
        <!--end::Page Scripts -->

    </body>
    <!-- end::Body -->

</html>