<?php

namespace App\Http\Controllers\Admin\Tasks;

use App\Models\Admin\CustomMap;
use Cassandra\Custom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use KJ\Core\controllers\AdminBaseController;
use KJLocalization;

class CustomMapController extends AdminBaseController {

    protected $model = 'App\Models\Admin\CustomMap';
    protected $mainViewName = 'admin.tasks.custommapmodal';

    public function modal(Request $request, int $id)
    {
        $item = CustomMap::find($id);
        $view = $this->index()
        ->with('item', $item);

        return response()->json([
            'viewDetail' => $view->render()
        ]);
    }
//
//    public function saveMap(Request $request){
//        $mapName = $request->get('MAP_NAME');
//        $userCreated = Auth::guard()->user()->ID;
//        CustomMap::
//    }


}