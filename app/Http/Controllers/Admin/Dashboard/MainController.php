<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Models\Admin\Core\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use KJ\Core\controllers\AdminBaseController;

class MainController extends AdminBaseController {

    public function index()
    {
        $prefix = Session::get('applocale') ? '/' . Session::get('applocale') : '';
        $redirect = '/admin';
        $user = Auth::guard()->user();

        if ($user->hasPermission(config('permission.CRM'))) {
            $redirect = '/admin/crm/relation';
        } else if ($user->hasPermission(config('permission.INTERVENTIES'))) {
            $redirect = '/admin/product';
        } else if ($user->hasPermission(config('permission.DOSSIERS'))) {
            $redirect = '/admin/project';
        } else if ($user->hasPermission(config('permission.TAKEN'))) {
            $redirect = '/admin/tasks';
        } else if ($user->hasPermission(config('permission.FACTURATIE'))) {
            $redirect = '/admin/invoice';
        }

        // Abort Unauthorized if user has none permission
        if ($redirect === '') {
            abort(403);
        }

        return redirect($prefix . $redirect);
    }

    public function notification(Request $request)
    {
        $id = ( $request->get('id') ?? 0);

        $notification = Notification::find($id);

        if ($notification) {
            $notification->READED = true;
            $result = $notification->save();

            return response()->json([
                'success' => $result
            ]);
        } else {
            return response()->json([
                'success' => false
            ]);
        }
    }
}