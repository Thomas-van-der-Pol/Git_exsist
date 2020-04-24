<?php

namespace App\Libraries\Core;

use App\Models\Core\Setting\SettingGroup;

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
}