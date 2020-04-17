<?php

namespace App\Http\Controllers\Consumer\Pages;

use App\Http\Controllers\Controller;

class PrivacyController extends Controller {

    public function index() {
        return view('consumer.pages.privacy');
    }

}