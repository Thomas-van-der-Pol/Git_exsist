@extends('theme.demo1.login', ['title' => KJLocalization::translate('Login', 'New password', 'New password')])

@section('content')

    <!--begin::Body-->
    <div class="kt-login__body">

        <!--begin::Signin-->
        <div class="kt-login__form kj-loginwrapper">
            <div class="kt-login__title">
                <h3>{{ KJLocalization::translate('Login', 'New password', 'New password') }}</h3>
            </div>

            <!--begin::Form-->
            <form class="kt-form form-login" novalidate="novalidate" action="{{ route('password.reset') }}" method="POST">
                {{ csrf_field() }}

                <input type="hidden" name="token" value="{{ $token }}">

                @if ($errors->has('EMAILADDRESS'))
                    <div class="m-alert m-alert--outline alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="{{ KJLocalization::translate('Algemeen', 'Sluiten', 'Sluiten') }}"></button>
                        <span>{{ $errors->first('EMAILADDRESS') }}</span>
                    </div>
                @endif
                @if ($errors->has('password'))
                    <div class="m-alert m-alert--outline alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="{{ KJLocalization::translate('Algemeen', 'Sluiten', 'Sluiten') }}"></button>
                        <span>{{ $errors->first('password') }}</span>
                    </div>
                @endif

                <div class="form-group">
                    <input id="EMAILADDRESS" type="email" placeholder="{{ KJLocalization::translate('Admin - Login', 'Email address', 'Email address') }}" class="form-control m-input" name="EMAILADDRESS" value="{{ old('EMAILADDRESS') }}" autocomplete="off" required autofocus>
                </div>
                <div class="form-group">
                    <input id="password" type="password" placeholder="{{ KJLocalization::translate('Admin - Login', 'Password', 'Password') }}" class="form-control m-input m-login__form-input--last" name="password" required>
                </div>
                <div class="form-group">
                    <input id="password_confirmation" type="password" placeholder="{{ KJLocalization::translate('Admin - Login', 'Confirm password', 'Confirm password') }}" class="form-control m-input m-login__form-input--last" name="password_confirmation" required>
                </div>

                <!--begin::Action-->
                <div class="kt-login__actions">
                    <button id="kt_login_signin_submit" class="btn btn-primary btn-elevate kt-login__btn-primary">
                        {{ KJLocalization::translate('Login', 'Change password', 'Change password') }}
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