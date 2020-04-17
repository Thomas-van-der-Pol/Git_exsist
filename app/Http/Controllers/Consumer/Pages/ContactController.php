<?php

namespace App\Http\Controllers\Consumer\Pages;

use App\Http\Controllers\Controller;

class ContactController extends Controller {

    public function index() {
        return view('consumer.pages.contact');
    }

}