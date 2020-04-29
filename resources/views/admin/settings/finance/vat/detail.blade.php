{{ Form::open(array(
    'method' => 'post',
    'id' => 'label_ledger',
    'class' => 'kt-form',
    'novalidate'
)) }}
<div class="kt-portlet__body kt-portlet__body--fit-inline-datatable">
    {{ Form::hidden('ID', $item ? $item->ID : -1) }}

    <div class="row">
        <div class="col-xl-4 col-lg-6">
            {{-- Required --}} {{ KJField::text('DESCRIPTION', KJLocalization::translate('Admin - Financieel', 'Omschrijving', 'Omschrijving'), $item ? $item->DESCRIPTION : '', true, ['required']) }}
            {{-- Required --}} {{ KJField::number('PERCENTAGE',KJLocalization::translate('Admin - Financieel', 'Percentage', 'Percentage'), $item ? number_format((float)$item->PERCENTAGE,0) : 0, true, ['required', 'min' => 0, 'steps' => 1], [
                'right' => [
                    ['type' => 'text', 'caption' => '&percnt;']
                ]
            ]) }}
            {{-- Required --}} {{ KJField::number('VATCODE',KJLocalization::translate('Admin - Financieel', 'Btw code', 'Btw code'), $item ? $item->VATCODE : 0, true, ['required', 'min' => 0, 'steps' => 1]) }}
        </div>
    </div>

    @if(!$item)
        {{ KJField::saveCancel(
            'btnSaveVatNew',
            'btnCancelVatNew',
            true,
            array(
                'saveText' => KJLocalization::translate('Algemeen', 'Toevoegen', 'Toevoegen'), 'saveStyle' => 'btn-success',
                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren'), 'cancelStyle' => 'btn-secondary'
            )
        ) }}
    @else
        {{ KJField::saveCancel(
            'btnSaveVat',
            'btnCancelVat',
            true,
            array(
                'saveText' => KJLocalization::translate('Algemeen', 'Opslaan', 'Opslaan'),
                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren')
            )
        ) }}
    @endif
</div>
{{ Form::close() }}