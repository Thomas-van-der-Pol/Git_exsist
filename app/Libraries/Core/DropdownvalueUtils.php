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

        $localeId = config('app.locale_id') ? config('app.locale_id') : config('language.defaultLangID');

        $valuesOri = DropdownValue::leftJoin('CORE_TRANSLATION', function($join) use ($localeId) {
                $join->on('FK_CORE_TRANSLATION_KEY', '=', 'TL_VALUE');
                $join->where('FK_CORE_LANGUAGE', $localeId);
            })
            ->where('CORE_DROPDOWNVALUE.ACTIVE', true)
            ->where('CORE_DROPDOWNVALUE.FK_CORE_DROPDOWNTYPE', $config_dropdowntype)
            ->orderByRaw('ISNULL(CORE_DROPDOWNVALUE.SEQUENCE, 0)')
            ->orderBy('CORE_TRANSLATION.TEXT')
            ->pluck('CORE_TRANSLATION.TEXT', 'CORE_DROPDOWNVALUE.ID');

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
    public static function getStatusDropdownTask($showNone = false)
    {
        $values = [
            0 => KJLocalization::translate('Algemeen', 'Niets geselecteerd', 'Niets geselecteerd') . '..',
            1 => KJLocalization::translate('Admin - Taken', 'Niet gestart', 'Niet gestart'),
            2 => KJLocalization::translate('Admin - Taken', 'Gestart', 'Gestart'),
            3 => KJLocalization::translate('Admin - Taken', 'Voltooid', 'Voltooid')
        ];

        return $values;
    }
}