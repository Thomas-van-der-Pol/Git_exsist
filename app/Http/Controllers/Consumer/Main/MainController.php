<?php

namespace App\Http\Controllers\Consumer\Main;

use Illuminate\Support\Facades\Session;
use KJ\Core\controllers\BaseController;

class MainController extends BaseController {

    protected $mainViewName = 'consumer.main.main';

    public function checkIndex()
    {
        $prefix = Session::get('applocale') ? '/' . Session::get('applocale') : '';

        return redirect($prefix . '/home');
    }
}
