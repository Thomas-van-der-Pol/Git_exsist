<?php

namespace App\Http\Controllers\Admin\Communicator;

use Illuminate\Support\Facades\Storage;
use KJ\Core\controllers\AdminBaseController;

class CommunicatorController extends AdminBaseController {

    public function download()
    {
        return Storage::disk('ftp')->download('bin/communicator/kj-communicator-2.0.0.exe');
    }

}