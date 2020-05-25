{{ Form::open(array(
    'method' => 'post',
    'id' => 'detailFormProductInvoiceScheme',
    'class' => 'kt-form',
)) }}
<div class="kt-portlet__body kt-portlet__body--fit-inline-datatable">
    {{ Form::hidden('ID', $item ? $item->ID : -1) }}
    <div class='col-lg-6'>
    {{--Required--}} {{ KJField::number('DAYS',KJLocalization::translate('Admin - Dossiers', 'Dagen tellen tot facturatie datum', 'Dagen tellen tot facturatie datum'), $item ? $item->DAYS : '' , true, ['required', 'min' => 0, 'steps' => 1]) }}
    {{--Required--}} {{ KJField::number('PERCENTAGE',KJLocalization::translate('Admin - Dossiers', 'Percentage', 'Percentage'), $item ? number_format($item->PERCENTAGE,2) : '', true, ['required', 'max'=> 100, 'min' => 0, 'steps' => 1]) }}
    </div>
    @if(!$item)
        {{ KJField::saveCancel(
            'btnSaveInvoiceSchemeNew',
            'btnCancelInvoiceSchemeNew',
            true,
            array(
                'saveText' => KJLocalization::translate('Algemeen', 'Toevoegen', 'Toevoegen'), 'saveStyle' => 'btn-success',
                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren'), 'cancelStyle' => 'btn-secondary'
            )
        ) }}
    @else
        {{ KJField::saveCancel(
            'btnSaveInvoiceScheme',
            'btnCancelInvoiceScheme',
            true,
            array(
                'saveText' => KJLocalization::translate('Algemeen', 'Opslaan', 'Opslaan'),
                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren')
            )
        ) }}
    @endif
</div>
{{ Form::close() }}