<?php

namespace App\Http\Controllers\Core\Setting;

use App\Models\Core\Setting\Setting;
use App\Models\Core\Setting\SettingValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use KJ\Core\controllers\AdminBaseController;
use KJLocalization;

class SettingController extends AdminBaseController
{
    protected $model = 'App\Models\Core\Setting\SettingGroup';

    protected $detailViewName = 'core.setting.group';

    protected function authorizeRequest($method, $parameters)
    {
        return Auth::guard()->user()->hasPermission(config('permission.INSTELLINGEN'));
    }

    protected function beforeDetail(int $ID, $item)
    {
        $settings = Setting::where([
                'ACTIVE' => true,
                'FK_CORE_SETTING_GROUP' => $ID
            ])
            ->orderBy('SEQUENCE')
            ->get();

        $bindings = array(
            ['settings', $settings],
        );

        return $bindings;
    }

    public function save(Request $request)
    {
        $settings = $request->get('SETTING');

        if (isset($settings)) {
            foreach ($settings as $key => $value)
            {
                $obj = SettingValue::firstOrCreate([
                    'FK_CORE_SETTING' => $key
                ]);
                $obj->VALUE_UNCONSTRAINED = $value;
                $obj->save();
            }
        }

        return response()->json([
            'success' => true,
            'new' => false
        ]);
    }

}