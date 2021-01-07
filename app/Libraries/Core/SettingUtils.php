<?php

namespace App\Libraries\Core;

use App\Models\Core\Setting\SettingGroup;
use App\Models\Core\Setting\SettingValue;

class SettingUtils
{
    public static function renderGroupsMenu()
    {
        $groups = SettingGroup::where([
            'ACTIVE' => true,
            'VISIBLE' => true
        ])->orderBy('SEQUENCE')->get();

        $view = view('core.setting.navigation')
            ->with('groups', $groups);

        return $view->render();
    }

    public static function get_testmode()
    {
        return SettingValue::getValue(config('setting.EMAIL_TESTMODE'));
    }

    public static function get_testmode_mail()
    {
        return SettingValue::getValue(config('setting.EMAIL_TEST_EMAIL'));
    }
}