<?php

namespace App\Http\Controllers\Admin\Settings\Host;

use App\Models\Core\Host;
use Illuminate\Http\Request;
use KJ\Core\controllers\AdminBaseController;

class HostController extends AdminBaseController
{
    protected $exceptAuthorization = ['getPrintersByHost'];

    protected $model = 'App\Models\Core\Host';
    protected $mainViewName = 'admin.settings.host.main';

    protected $allColumns = ['ID', 'ACTIVE', 'HOSTNAME', 'MAC_ADDRESS', 'PRINTER_DEFAULT', 'PRINTER_INVOICE'];

    protected $datatableFilter = array(
        ['HOSTNAME', array(
            'param' => 'hostSearch',
            'operation' => 'like'
        )]
    );

    protected $datatableDefaultSort = array(
        [
            'field' => 'HOSTNAME',
            'sort'  => 'ASC'
        ]
    );

    protected $detailViewName = 'admin.settings.host.detail';

    protected $saveUnsetValues = [
        'PRINTER_DEFAULT_DUMMY',
        'PRINTER_INVOICE_DUMMY'
    ];

    protected $saveValidation = [
        'HOSTNAME' => 'required',
        'PRINTER_DEFAULT' => 'required'
    ];

//    protected function authorizeRequest($method, $parameters)
//    {
//        return Auth::guard('admin')->user()->hasPermission(config('permission.INSTELLINGEN'));
//    }

    public function getPrintersByHost(Request $request)
    {
        $name = $request->get('name');
        $macAddress = $request->get('mac');

        $host = Host::where([
            'HOSTNAME' => $name,
            'MAC_ADDRESS' => $macAddress
        ])->first();

        return response()->json([
            'success' => ($host != null),
            'host' => $host
        ], 200);
    }
}