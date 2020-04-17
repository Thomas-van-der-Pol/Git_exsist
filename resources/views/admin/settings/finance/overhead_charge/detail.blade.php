{{ Form::open(array(
    'method' => 'post',
    'id' => 'label_overhead_charge',
    'class' => 'kt-form',
    'novalidate'
)) }}
<div class="kt-portlet__body kt-portlet__body--fit-inline-datatable">
    {{ Form::hidden('ID', $item ? $item->ID : -1) }}

    <div class="row">
        <div class="col-xl-4 col-lg-6">
            {{-- Required --}} {{ KJField::date('DATE_START', KJLocalization::translate('Admin - Financieel', 'Van', 'Van'), $item ? $item->getDateStartFormattedAttribute() : '', ['required', 'data-locale-format' => \KJ\Localization\libraries\LanguageUtils::getJSDatePickerFormat()]) }}
            {{-- Required --}} {{ KJField::date('DATE_END', KJLocalization::translate('Admin - Financieel', 'Tot', 'Tot'), $item ? $item->getDateEndFormattedAttribute() : '', ['required', 'data-locale-format' => \KJ\Localization\libraries\LanguageUtils::getJSDatePickerFormat()]) }}
            {{-- Required --}} {{ KJField::number('PERCENTAGE',KJLocalization::translate('Admin - Financieel', 'Percentage', 'Percentage'), $item ? number_format((float)$item->PERCENTAGE,0) : 0, true, ['required', 'min' => 1, 'steps' => 1], [
                'right' => [
                    ['type' => 'text', 'caption' => '&percnt;']
                ]
            ]) }}
        </div>
    </div>

    @if(!$item)
        {{ KJField::saveCancel(
            'btnSaveOverheadChargeNew',
            'btnCancelOverheadChargeNew',
            true,
            array(
                'saveText' => KJLocalization::translate('Algemeen', 'Toevoegen', 'Toevoegen'), 'saveStyle' => 'btn-success',
                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren'), 'cancelStyle' => 'btn-secondary'
            )
        ) }}
    @else
        {{ KJField::saveCancel(
            'btnSaveOverheadCharge',
            'btnCancelOverheadCharge',
            true,
            array(
                'saveText' => KJLocalization::translate('Algemeen', 'Opslaan', 'Opslaan'),
                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren')
            )
        ) }}
    @endif
</div>
{{ Form::close() }}