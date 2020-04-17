{{ Form::open(array(
    'method' => 'post',
    'id' => 'detailFormDropdownValue',
    'class' => 'kt-form',
    'novalidate'
)) }}
<div class="kt-portlet__body kt-portlet__body--fit-inline-datatable py-4">
    {{ Form::hidden('ID', $item ? $item->ID : -1) }}

    <div class="row">
        <div class="col-xl-6 col-lg-8">
            {{--Required--}} {{ KJField::select('ACTIVE', KJLocalization::translate('Algemeen', 'Status', 'Status'), $status, $item ? $item->ACTIVE : true, true, 0, ['required']) }}
            {{ KJField::number('SEQUENCE', KJLocalization::translate('Algemeen', 'Volgorde', 'Volgorde'), $item ? $item->SEQUENCE : '') }}
            {{--Required--}} {{ KJField::translationtext('TL_VALUE', KJLocalization::translate('Algemeen', 'Omschrijving', 'Omschrijving'), $item ? $item->TL_VALUE : -1, ['required']) }}
        </div>
    </div>

    @if(!$item)
        {{ KJField::saveCancel(
            'btnSaveDropdownValueNew',
            'btnCancelDropdownValueNew',
            true,
            array(
                'saveText' => KJLocalization::translate('Algemeen', 'Toevoegen', 'Toevoegen'), 'saveStyle' => 'btn-success',
                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren'), 'cancelStyle' => 'btn-secondary'
            )
        ) }}
    @else
        {{ KJField::saveCancel(
            'btnSaveDropdownValue',
            'btnCancelDropdownValue',
            true,
            array(
                'saveText' => KJLocalization::translate('Algemeen', 'Opslaan', 'Opslaan'),
                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren')
            )
        ) }}
    @endif
</div>
{{ Form::close() }}
