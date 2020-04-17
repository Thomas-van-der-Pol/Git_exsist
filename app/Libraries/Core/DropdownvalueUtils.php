<?php

namespace App\Libraries\Core;

use App\Models\Core\DropdownValue;
use KJLocalization;

class DropdownvalueUtils
{
    public static function getDropdown($config_dropdowntype, $showNone = true)
    {
        if($showNone) {
            $none = ['' => KJLocalization::translate('Algemeen', 'Niets geselecteerd', 'Niets geselecteerd') . '..'];
        }

        $valuesOri = DropdownValue::all()
            ->where('ACTIVE', true)
            ->where('FK_CORE_DROPDOWNTYPE',$config_dropdowntype)
            ->sortBy('SEQUENCE')
            ->pluck('value', 'ID');


        $values = ($showNone ? ($values = $none + $valuesOri->toArray()) : ($valuesOri->toArray()) );

        return $values;
    }

    public static function getStatusDropdown($showNone = true)
    {
        if($showNone) {
            $none = ['' => KJLocalization::translate('Algemeen', 'Niets geselecteerd', 'Niets geselecteerd') . '..'];
        }

        $values = [
            1 => KJLocalization::translate('Algemeen', 'Actief', 'Actief'),
            0 => KJLocalization::translate('Algemeen', 'Gearchiveerd', 'Gearchiveerd')
        ];

        $values = ($showNone ? ($values = $none + $values) : $values);

        return $values;
    }
}