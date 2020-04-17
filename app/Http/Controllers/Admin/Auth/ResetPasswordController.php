<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use KJLocalization;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    protected function guard()
    {
        return Auth::guard('admin');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:admin');
    }

    /**
     * Override want ander e-mailadres veld
     *
     * @return array
     */
    protected function rules() {
        return [
            'token'         => 'required',
            'EMAILADDRESS'  => 'required|email',
            'password'      => 'required|confirmed|min:8|regex:/(^(?=.*[#?!@$%^&*-])[0-9a-zA-Z#?!@$%^&*-]{8,})/u',
        ];
    }

    /**
     * Get the password reset validation error messages.
     *
     * @return array
     */
    protected function validationErrorMessages()
    {
        return [
            'password.regex' => KJLocalization::translate('Admin - My profile', 'Error password format', 'Password must consist of 8 characters and at least 1 special character.')
        ];
    }

    /**
     * Override
     */
    protected function credentials(Request $request)
    {
        return array_merge($request->only('EMAILADDRESS', 'password', 'password_confirmation', 'token'), ['ACTIVE' => TRUE, 'LOGIN_ENABLED' => TRUE]);
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->PASSWORD = Hash::make($password);

        $user->setRememberToken(Str::random(60));

        $user->save();

        event(new PasswordReset($user));

        $this->guard()->login($user);
    }

    protected function sendResetFailedResponse(Request $request, $response) {
        return redirect()->back()
            ->withInput($request->only('EMAILADDRESS'))
            ->withErrors(['EMAILADDRESS' => trans($response)]);
    }

    /*
     * Override want andere view
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('admin.auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker('admins');
    }
}
