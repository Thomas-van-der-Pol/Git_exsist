<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

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

    /*
    * Username is een standaard functie dus zo laten staan
    * op deze manier zorgen we dat hier altijd netjes emailadres terugkomt
    */
    public function username() {
        return 'EMAILADDRESS';
    }

    /**
     * Override omdat we afwijkend veld gebruiken
     */
    protected function validateEmail(Request $request) {
        $this->validate($request, ['EMAILADDRESS' => 'required|email']);
    }

    /**
     * Override omdat we afwijkend veld gebruiken
     */
    public function sendResetLinkEmail(Request $request) {
        $this->validate($request, ['EMAILADDRESS' => 'required|email']);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendResetLink($request->only('EMAILADDRESS'));

        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($request,$response)
            : $this->sendResetLinkFailedResponse($request, $response);
    }

    /**
     * Override dat de response terug komt in AJAX formaat
     *
     */
    protected function sendResetLinkResponse(Request $request,$response) {
        return [
            'success'   => true,
            'status'    => trans($response)
        ];
    }

    /**
     * Override dat de response terug komt in AJAX formaat
     */
    protected function sendResetLinkFailedResponse(Request $request, $response) {
        return [
            'success'   => false,
            'msg'       => trans($response)
        ];
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
