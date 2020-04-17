<?php

namespace App\Http\Controllers\Consumer\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use KJ\Localization\libraries\LanguageUtils;

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
        return Auth::guard('web');
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /*
    * Username is een standaard functie dus zo laten staan
    * op deze manier zorgen we dat hier altijd netjes emailadres terugkomt
    */
    public function username() {
        return 'EMAILADDRESS';
    }

    public function index() {
        return view('consumer.auth.login');
    }

    public function login(Request $request)
    {
        return $this->parent_login($request);
    }

    protected function authenticated(Request $request, $user)
    {
        $languageId = config('language.defaultLangID');
        if ($user->family) {
            $languageId = $user->family->FK_CORE_LANGUAGE;
            $this->redirectTo = 'relocation';
        } else if ($user->client) {
            $languageId = $user->client->FK_CORE_LANGUAGE;
            $this->redirectTo = '/';
        }

        $array = config('language.langs');
        $keys = array_keys(array_column($array, 'ID'), $languageId);
        $new_array = array_map(function($k) use ($array) {
            return $array[$k];
        },
            $keys
        );

        $locale = strtolower($new_array[0]['CODE'] ?? '');

        if ($locale != '') {
            Session::put('applocale', $locale);
        }

        $this->redirectTo = LanguageUtils::getUrl($this->redirectTo);
    }

    /**
     * Override voor het uitloggen
     * nodig voor ontkoppelen relatie bij basket
     */
    public function logout(Request $request)
    {
        session()->forget('consumer.relation_id');
        $this->guard()->logout();

        return redirect('/');
    }

    protected function credentials(Request $request) {
        return array_merge($request->only($this->username(), 'password'), ['ACTIVE' => TRUE, 'CLIENT_ACTIVE' => TRUE]);
    }
}