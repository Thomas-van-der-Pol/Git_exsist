<?php

namespace App\Http\Controllers\Admin\Auth;

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
        $this->middleware('guest:admin')->except('logout');
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
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $this->redirectTo = ($request->get('ORIGIN') ? $request->get('ORIGIN') : '/admin');

        return $this->parent_login($request);
    }

    /**
     * Override voor het uitloggen
     * nodig voor ontkoppelen relatie bij basket
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        return redirect('/admin');
    }

    protected function credentials(Request $request) {
        return array_merge($request->only($this->username(), 'password'), ['ACTIVE' => TRUE, 'LOGIN_ENABLED' => TRUE]);
    }
}
