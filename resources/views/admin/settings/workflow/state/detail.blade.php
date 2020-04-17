{{ Form::open(array(
    'method' => 'post',
    'id' => 'workflow_state',
    'class' => 'kt-form',
    'novalidate'
)) }}
<div class="kt-portlet__body kt-portlet__body--fit-inline-datatable">
    {{ Form::hidden('ID', $item ? $item->ID : -1) }}

    <div class="row">
        <div class="col-xl-4 col-lg-6">
            {{-- Required --}} {{ KJField::text('DESCRIPTION', KJLocalization::translate('Admin - Workflows', 'Omschrijving', 'Omschrijving'), $item ? $item->DESCRIPTION : '', true, ['required']) }}
            {{-- Required --}} {{ KJField::text('ACTION_DESCRIPTION', KJLocalization::translate('Admin - Workflows', 'Actie omschrijving', 'Actie omschrijving'), $item ? $item->ACTION_DESCRIPTION : '', true, ['required']) }}
        </div>
    </div>

    @if(!$item)
        {{ KJField::saveCancel(
            'btnSaveStateNew',
            'btnCancelStateNew',
            true,
            array(
                'saveText' => KJLocalization::translate('Algemeen', 'Toevoegen', 'Toevoegen'), 'saveStyle' => 'btn-success',
                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren'), 'cancelStyle' => 'btn-secondary'
            )
        ) }}
    @else
        {{ KJField::saveCancel(
            'btnSaveState',
            'btnCancelState',
            true,
            array(
                'saveText' => KJLocalization::translate('Algemeen', 'Opslaan', 'Opslaan'),
                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren')
            )
        ) }}
    @endif
</div>
{{ Form::close() }}