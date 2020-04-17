<?php

namespace App\Http\Controllers\Consumer\Document\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers {
        logout as protected parent_logout;
        login as protected parent_login;
    }

    protected $redirectTo = '/';

    protected function guard()
    {
        return Auth::guard('document');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:document')->except('logout');
    }

    /*
    * Username is een standaard functie dus zo laten staan
    * op deze manier zorgen we dat hier altijd netjes emailadres terugkomt
    */
    public function username()
    {
        return 'EMAILADDRESS';
    }

    public function index()
    {
        // Save previous URL which send us to the login form
        session(['shared_document_link' => url()->previous()]);

        return view('consumer.document.auth.login');
    }

    public function login(Request $request)
    {
        // Redirect to original URL which send us to the login form
        $this->redirectTo = session('shared_document_link', $this->redirectTo);

        return $this->parent_login($request);
    }

    /**
     * Override voor het uitloggen
     * nodig voor ontkoppelen relatie bij basket
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        return redirect('/');
    }

    protected function credentials(Request $request) {
        return array_merge($request->only($this->username(), 'password'), ['ACTIVE' => TRUE]);
    }
}
