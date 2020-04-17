{{ Form::open(array(
    'method' => 'post',
    'id' => 'detailFormContent_' . $item->ID,
    'class' => 'kt-form',
    'novalidate'
)) }}
<div class="kt-portlet__body">
    {{ Form::hidden('ID', $item ? $item->ID : -1) }}
    {{ Form::hidden('FK_TABLE', $item ? $item->FK_TABLE : '') }}
    {{ Form::hidden('FK_ITEM', $item ? $item->FK_ITEM : -1) }}

    {{--Required--}} {{ KJField::translationtext('TL_TITLE', KJLocalization::translate('Admin - Content', 'Title', 'Title'), $item->TL_TITLE, ['required']) }}
    {{ KJField::translationtext('TL_CONTENT', KJLocalization::translate('Admin - Content', 'Content', 'Content'), $item->TL_CONTENT, ['rows' => 12, 'class' => 'summernote'], true) }}

    {{ KJField::saveCancel(
        'btnSaveContentItem',
        'btnCancelContentItem',
        true,
        array(
            'saveText' => KJLocalization::translate('Algemeen', 'Opslaan', 'Opslaan'),
            'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren')
        )
    ) }}
</div>
{{ Form::close() }}