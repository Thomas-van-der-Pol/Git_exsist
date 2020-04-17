<?php setlocale(LC_ALL, 'nl_NL'); ?>
{{ Form::open(array('method' => 'post','id' => 'detailFormUsercontract','class' => 'kt-form','novalidate')) }}
<div class="kt-portlet__body kt-portlet__body--fit-inline-datatable">
    {{ Form::hidden('ID', $item ? $item->ID : -1) }}

    <div class="row">
        <div class="col-xl-6 col-lg-6">
            {{ KJField::select('FK_CORE_DROPDOWNVALUE_USERCONTRACTTYPE', KJLocalization::translate('Admin - Werknemers', 'Contracttype', 'Contracttype'), $contracttypes, $item ? $item->FK_CORE_DROPDOWNVALUE_USERCONTRACTTYPE : '', true, config('dropdown_type.TYPE_USERCONTRACTTYPE'),['required']) }}
            {{ KJField::number('HOURS',KJLocalization::translate('Admin - Werknemers', 'Contracturen', 'Contracturen'), $item ? number_format((float)$item->HOURS,0, '.', '') : 0, true, ['required','min' => 1,'steps'=>1]) }}
            {{ KJField::number('HOURS_WEEKLY',KJLocalization::translate('Admin - Werknemers', 'Standaard uren week', 'Standaard uren week'), $item ? number_format((float)$item->HOURS_WEEKLY,0, '.', '') : 0, true, ['required','min' => 1,'steps'=>1]) }}
            {{ KJField::number('HOURLY_WAGE',KJLocalization::translate('Admin - Werknemers', 'Kosten per uur', 'Kosten per uur'), $item ? number_format((float)$item->HOURLY_WAGE,2, '.', '') : 0, true, [], ['right' => [['type' => 'text', 'caption' => '&euro;']] ]) }}
            {{ KJField::date('DATE_START', KJLocalization::translate('Admin - Werknemers', 'Startdatum', 'Startdatum'), $item ? $item->getDateStartFormattedAttribute() : '', ['required', 'data-locale-format' => \KJ\Localization\libraries\LanguageUtils::getJSDatePickerFormat()]) }}
            {{ KJField::date('DATE_PROBATION', KJLocalization::translate('Admin - Werknemers', 'Einde proeftijd', 'Einde proeftijd'), $item ? $item->getDateProbationFormattedAttribute() : '', ['data-locale-format' => \KJ\Localization\libraries\LanguageUtils::getJSDatePickerFormat()]) }}
            {{ KJField::date('DATE_END', KJLocalization::translate('Admin - Werknemers', 'Einddatum', 'Einddatum'), $item ? $item->getDateEndFormattedAttribute() : '', ['data-locale-format' => \KJ\Localization\libraries\LanguageUtils::getJSDatePickerFormat()]) }}
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6 col-lg-6">
            <table class="table">
                <thead>
                <tr>
                    <th colspan="7">
                        {{ KJLocalization::translate('Admin - Werknemers', 'Even week', 'Even week') }}
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    @for($x = 1; $x <= 7; $x++)
                        @php($currentDay = $days->where('DAY', $x)->where('EVEN_WEEK', true)->first())
                        <td>
                            {{ KJField::number('HOURS_EVEN['.$x.']', strtoupper(substr(strftime('%a', strtotime("Sunday + $x Days")), 0, 1)), $currentDay ? number_format((float)$currentDay->HOURS,0, '.', '') : '', true, ['required','min' => 0,'steps'=>1]) }}
                        </td>
                    @endfor
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6 col-lg-6">
            <table class="table">
                <thead>
                <tr>
                    <th colspan="7">
                        {{ KJLocalization::translate('Admin - Werknemers', 'Oneven week', 'Oneven week') }}
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    @for($x = 1; $x <= 7; $x++)
                        @php($currentDay = $days->where('DAY', $x)->where('EVEN_WEEK', false)->first())
                        <td>
                            {{ KJField::number('HOURS_ODD['.$x.']', strtoupper(substr(strftime('%a', strtotime("Sunday + $x Days")), 0, 1)), $currentDay ? number_format((float)$currentDay->HOURS,0, '.', '') : '', true, ['required','min' => 0,'steps'=>1]) }}
                        </td>
                    @endfor
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <h5 class="mb-3">
                {{ KJLocalization::translate('Admin - Werknemers', 'Notificaties', 'Notificaties') }}
            </h5>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6 col-lg-6">
            {{ KJField::checkbox('DUMMY_NOTIFICATION', KJLocalization::translate('Admin - Werknemers', 'Notificatie einde contract/proeftijd ontvangen', 'Notificatie einde contract/proeftijd ontvangen'), true, false, true) }}
            <div class="SHOW_NOTIFICATION {{ ( $item && ($item->GET_NOTIFICATION == true) ) ? '' : 'kt-hide' }}">
                {{ KJField::select('DUMMY_FK_CORE_ROLE_NOTIFICATION', KJLocalization::translate('Admin - Werknemers', 'Notificatie rol', 'Notificatie rol'), $roles, '', true, 0) }}
                {{ KJField::date('DUMMY_DATE_NOTIFICATION', KJLocalization::translate('Admin - Werknemers', 'Notificatie datum', 'Notificatie datum'), '', ['data-locale-format' => \KJ\Localization\libraries\LanguageUtils::getJSDatePickerFormat()]) }}
            </div>
        </div>
    </div>

    @if(!$item)
        {{ KJField::saveCancel(
            'btnSaveUsercontractNew',
            'btnCancelUsercontractNew',
            true,
            array(
                'saveText' => KJLocalization::translate('Algemeen', 'Toevoegen', 'Toevoegen'), 'saveStyle' => 'btn-success',
                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren'), 'cancelStyle' => 'btn-secondary'
            )
        ) }}
    @else
        {{ KJField::saveCancel(
            'btnSaveUsercontract',
            'btnCancelUsercontract',
            true,
            array(
                'saveText' => KJLocalization::translate('Algemeen', 'Opslaan', 'Opslaan'),
                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren')
            )
        ) }}
    @endif
</div>
{{ Form::close() }}