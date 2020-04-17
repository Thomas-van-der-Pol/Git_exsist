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
                    <button id="kt_login_signin_submit" class="btn btn-primary btn-elevate kt-login__btn-primary">
                        {{ KJLocalization::translate('Login', 'Inloggen', 'Inloggen') }}
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