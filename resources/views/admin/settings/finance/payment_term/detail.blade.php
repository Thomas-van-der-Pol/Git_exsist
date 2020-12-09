{{ Form::open([
    'method' => 'post',
    'id' => 'label_payment_term',
    'class' => 'kt-form',
    'novalidate'
]) }}
    <div class="kt-portlet__body kt-portlet__body--fit-inline-datatable">
        {{ Form::hidden('ID', $item ? $item->ID : -1) }}

        <div class="row">
            <div class="col-xl-4 col-lg-6">
                {{-- Required --}} {{ KJField::text('DESCRIPTION', KJLocalization::translate('Admin - Financieel', 'Omschrijving', 'Omschrijving'), $item ? $item->DESCRIPTION : '', true, ['required']) }}
                {{ KJField::number('AMOUNT_DAYS', KJLocalization::translate('Admin - Financieel', 'Aantal dagen', 'Aantal dagen'), $item ? $item->AMOUNT_DAYS : 0, true, ['min' => 0, 'step' => 1]) }}
                {{ KJField::number('CODE', KJLocalization::translate('Admin - Financieel', 'Code', 'Code'), $item ? $item->CODE : 0, true, ['min' => 0, 'step' => 1]) }}
                {{ KJField::checkbox('DEFAULT', KJLocalization::translate('Admin - Financieel', 'Standaard', 'Standaard'), true, $item ? $item->DEFAULT : false, true) }}
            </div>
        </div>

        @if(!$item)
            {{ KJField::saveCancel(
                'btnSavePaymentTermNew',
                'btnCancelPaymentTermNew',
                true,
                [
                    'saveText' => KJLocalization::translate('Algemeen', 'Toevoegen', 'Toevoegen'), 'saveStyle' => 'btn-success',
                    'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren'), 'cancelStyle' => 'btn-secondary'
                ]
            ) }}
        @else
            {{ KJField::saveCancel(
                'btnSavePaymentTerm',
                'btnCancelPaymentTerm',
                true,
                [
                    'saveText' => KJLocalization::translate('Algemeen', 'Opslaan', 'Opslaan'),
                    'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren')
                ]
            ) }}
        @endif
    </div>
{{ Form::close() }}