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
            {{-- Required --}} {{ KJField::number('ACCOUNT',KJLocalization::translate('Admin - Financieel', 'Grootboeknummer', 'Grootboeknummer'), $item ? $item->ACCOUNT : '', true, ['required', 'min' => 1, 'step' => 1]) }}
        </div>
    </div>

    @if(!$item)
        {{ KJField::saveCancel(
            'btnSaveLedgerNew',
            'btnCancelLedgerNew',
            true,
            array(
                'saveText' => KJLocalization::translate('Algemeen', 'Toevoegen', 'Toevoegen'), 'saveStyle' => 'btn-success',
                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren'), 'cancelStyle' => 'btn-secondary'
            )
        ) }}
    @else
        {{ KJField::saveCancel(
            'btnSaveLedger',
            'btnCancelLedger',
            true,
            array(
                'saveText' => KJLocalization::translate('Algemeen', 'Opslaan', 'Opslaan'),
                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren')
            )
        ) }}
    @endif
</div>
{{ Form::close() }}