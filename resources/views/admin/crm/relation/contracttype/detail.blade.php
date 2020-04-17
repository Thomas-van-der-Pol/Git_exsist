{{ Form::open(array(
    'method' => 'post',
    'id' => 'detailFormContracttype',
    'class' => 'kt-form',
    'novalidate'
)) }}
<div class="kt-portlet__body kt-portlet__body--fit-inline-datatable">
    {{ Form::hidden('ID', $item ? $item->ID : -1) }}

     {{ KJField::toggleswitch('ACTIVE', KJLocalization::translate('Algemeen', 'Actief', 'Actief'), ($item->ACTIVE ?? 1)) }}
    {{--Required--}} {{ KJField::translationtext('TL_TITLE', KJLocalization::translate('Admin - CRM', 'Title', 'Title'), $item ? $item->TL_TITLE : -1, ['required']) }}
    {{--Required--}} {{ KJField::translationtext('TL_VALUE', KJLocalization::translate('Admin - CRM', 'Value', 'Value'), $item ? $item->TL_VALUE : -1, ['required']) }}

    @if(!$item)
        {{ KJField::saveCancel(
            'btnSaveContracttypeNew',
            'btnCancelContracttypeNew',
            true,
            array(
                'saveText' => KJLocalization::translate('Algemeen', 'Toevoegen', 'Toevoegen'), 'saveStyle' => 'btn-success',
                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren'), 'cancelStyle' => 'btn-secondary'
            )
        ) }}
    @else
        {{ KJField::saveCancel(
            'btnSaveContracttype',
            'btnCancelContracttype',
            true,
            array(
                'saveText' => KJLocalization::translate('Algemeen', 'Opslaan', 'Opslaan'),
                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren')
            )
        ) }}
    @endif
</div>
{{ Form::close() }}