@extends('theme.demo1.login', ['title' => KJLocalization::translate('Login', 'Login', 'Login')])

@section('content')
    <!--begin::Body-->
    <div class="kt-login__body">
        <!--begin::Signin-->
        <div class="kt-login__form kj-loginwrapper">
            <div class="kt-login__title">
                <h3>{{ KJLocalization::translate('Login', 'Inloggen', 'Inloggen') }}</h3>
            </div>

            <!--begin::Form-->
            <form class="kt-form form-login" novalidate="novalidate" action="@yield('submitRoute')" method="POST">
                {{ csrf_field() }}

                @if((env('APP_ENV') != 'production') || env('APP_DEBUG'))
                    <div class="alert alert-solid-danger alert-bold fade show" role="alert">
                        <div class="alert-text">
                            <strong>{{ strtoupper(KJLocalization::translate('Algemeen', 'Aanduiding omgeving', 'Geselecteerde omgeving')) }}</strong>: {{ strtoupper(env('APP_ENV')) }}<br/>
                            <strong>{{ strtoupper(KJLocalization::translate('Algemeen', 'Debug mode', 'Debug mode')) }}</strong>: {{ strtoupper(env('APP_DEBUG') ? 'aan' : 'uit') }}
                        </div>
                    </div>
                @endif

                @if ($errors->has('EMAILADDRESS'))
                    <div class="m-alert m-alert--outline alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="{{ KJLocalization::translate('Algemeen', 'Sluiten', 'Sluiten') }}"></button>
                        <span>{{ $errors->first('EMAILADDRESS') }}</span>
                    </div>
                @endif

                <div class="form-group">
                    <input id="EMAILADDRESS" type="email" placeholder="{{ KJLocalization::translate('Login', 'E-mailadres', 'E-mailadres') }}" class="form-control m-input" name="EMAILADDRESS" value="{{ old('EMAILADDRESS') }}" autocomplete="off" required autofocus>
                </div>
                <div class="form-group">
                    <input id="password" type="password" placeholder="{{ KJLocalization::translate('Login', 'Wachtwoord', 'Wachtwood') }}" class="form-control m-input m-login__form-input--last" name="password" required>
                </div>

                <!--begin::Action-->
                <div class="kt-login__actions">
                    <a href="javascript:;" id="kt_login_forgot" class="kt-link kt-login__link">{{ KJLocalization::translate('Login', 'Wachtwoord vergeten', 'Wachtwoord vergeten') }} ?</a>
                    <button id="kt_login_signin_submit" class="btn btn-primary btn-elevate kt-login__btn-primary">
                        {{ KJLocalization::translate('Login', 'Inloggen', 'Inloggen') }}
                    </button>
                </div>

                <!--end::Action-->
            </form>
            <!--end::Form-->

{{--            <div class="kt-login__divider">--}}
{{--                <div class="kt-divider">--}}
{{--                    <span></span>--}}
{{--                    <span>{{ KJLocalization::translate('Login', 'Choose different language', 'Choose different language') }}</span>--}}
{{--                    <span></span>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <div class="kt-login__options">--}}
{{--                @foreach(config('language.langs') as $lang)--}}
{{--                    @php($language = \App\Models\Admin\Core\Language::find($lang['ID']))--}}

{{--                    <a href="/admin/changeLanguage/{{ $lang['CODE'] }}" class="btn kt-btn">--}}
{{--                        {!! Html::image($lang['ICONPATH'], $lang['DESCRIPTION'], ['height' => '16', 'class' => 'mr-2']) !!}--}}
{{--                        {{ $language ? $language->getLanguageDescriptionAttribute($lang['ID']) : $lang['DESCRIPTION'] }}--}}
{{--                    </a>--}}
{{--                @endforeach--}}
{{--            </div>--}}
        </div>
        <!--end::Signin-->

        <!--begin::Signin-->
        <div class="kt-login__form kj-passwordforgetwrapper">
            <div class="kt-login__title">
                <h3>{{ KJLocalization::translate('Login', 'Wachtwoord vergeten', 'Wachtwoord vergeten') }}</h3>
            </div>

            <!--begin::Form-->
            <form class="kt-form form-forget" novalidate="novalidate" action="{{ route('admin.password.request') }}" method="POST">
                {{ csrf_field() }}

                @if ($errors->has('EMAILADDRESS'))
                    <div class="m-alert m-alert--outline alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="{{ KJLocalization::translate('Algemeen', 'Sluiten', 'Sluiten') }}"></button>
                        <span>{{ $errors->first('EMAILADDRESS') }}</span>
                    </div>
                @endif

                <div class="form-group">
                    <input type="email" placeholder="{{ KJLocalization::translate('Login', 'E-Mailadres', 'E-mailadres') }}" class="form-control m-input" name="EMAILADDRESS" value="{{ old('EMAILADDRESS_FORGET') }}" autocomplete="off" required autofocus>
                </div>

                <!--begin::Action-->
                <div class="kt-login__actions">
                    <button id="kt_login_forgot_submit" class="btn btn-primary btn-elevate kt-login__btn-primary">
                        {{ KJLocalization::translate('Login', 'Aanvragen', 'Aanvragen') }}
                    </button>
                    <button id="kt_login_forgot_cancel" class="btn btn-pill kt-login__btn-secondary">
                        {{ KJLocalization::translate('Login', 'Annuleren', 'Annuleren') }}
                    </button>
                </div>

                <!--end::Action-->
            </form>
            <!--end::Form-->
        </div>
        <!--end::Signin-->



    </div>

    <!--end::Body-->
@endsection