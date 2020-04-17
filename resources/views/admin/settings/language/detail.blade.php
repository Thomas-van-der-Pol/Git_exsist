{{ Form::open(array(
    'method' => 'post',
    'id' => 'detailFormLanguage',
    'class' => 'kt-form',
    'novalidate'
)) }}
<div class="kt-portlet__body kt-portlet__body--fit-inline-datatable">
    {{ Form::hidden('ID', $item ? $item->ID : -1) }}

    {{ KJField::text('DESCRIPTION', KJLocalization::translate('Admin - Language', 'Description', 'Description'), $item ? $item->DESCRIPTION : '',true,['required']) }}
    {{--Required--}} {{ KJField::translationtext('TL_DESCRIPTION', KJLocalization::translate('Admin - Language', 'Description translation', 'Description translation'), $item ? $item->TL_DESCRIPTION : -1, ['required']) }}

    @if(!$item)
        {{ KJField::saveCancel(
            'btnSaveLanguageNew',
            'btnCancelLanguageNew',
            true,
            array(
                'saveText' => KJLocalization::translate('Algemeen', 'Toevoegen', 'Toevoegen'), 'saveStyle' => 'btn-success',
                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren'), 'cancelStyle' => 'btn-secondary'
            )
        ) }}
    @else
        {{ KJField::saveCancel(
            'btnSaveLanguage',
            'btnCancelLanguage',
            true,
            array(
                'saveText' => KJLocalization::translate('Algemeen', 'Opslaan', 'Opslaan'),
                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren')
            )
        ) }}
    @endif
</div>
{{ Form::close() }}