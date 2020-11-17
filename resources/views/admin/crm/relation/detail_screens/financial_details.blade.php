<div class="kt-portlet__body">
    {{ Form::open(array(
        'method' => 'post',
        'id' => 'detailFormFinancialDetails',
        'class' => 'kt-form',
        'novalidate'
    )) }}
    {{ Form::hidden('ID', $item ? $item->ID : -1) }}

        <div class="row">
            <div class="col">
                <h5 class="mb-3">
                    {{ KJLocalization::translate('Admin - CRM', 'Financiële gegevens', 'Financiële gegevens') }}
                    <button type="button" class="btn btn-sm btn-icon setEditMode" data-target="financial_details">
                        <i class="fa fa-pen"></i>
                    </button>
                </h5>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-4 col-lg-6">
                {{ KJField::number('NUMBER_DEBTOR', KJLocalization::translate('Admin - CRM', 'Debiteurnummer', 'Debiteurnummer'), $item ? $item->NUMBER_DEBTOR : '', true, ['data-screen-mode' => 'read, edit'], [
                     'right' => [
                          ['type' => 'button', 'caption' => '<i class="la la-refresh"></i>', 'class' => 'btn btn btn-label-brand btn-square btn-bold btn-upper btn-sm btn-icon generateDebtornumber', 'options' => ['data-id' => $item->ID]]
                     ]
                ]) }}

                {{ KJField::number('NUMBER_CREDITOR', KJLocalization::translate('Admin - CRM', 'Crediteurnummer', 'Crediteurnummer'), $item ? $item->NUMBER_CREDITOR : '', true, ['data-screen-mode' => 'read, edit'], [
                     'right' => [
                          ['type' => 'button', 'caption' => '<i class="la la-refresh"></i>', 'class' => 'btn btn btn-label-brand btn-square btn-bold btn-upper btn-sm btn-icon generateCreditornumber', 'options' => ['data-id' => $item->ID]]
                     ]
                ]) }}

                @if($item->VAT_LIABLE)
                    {{ KJField::text('VAT_NUMBER', KJLocalization::translate('Admin - CRM', 'Btw nummer', 'Btw nummer'), $item ? $item->VAT_NUMBER : '', true, ['data-screen-mode' => 'read, edit']) }}
                @else
                    {{--Required--}} {{ KJField::text('VAT_NUMBER', KJLocalization::translate('Admin - CRM', 'Btw nummer', 'Btw nummer'), $item ? $item->VAT_NUMBER : '', true, ['required', 'data-screen-mode' => 'read, edit']) }}
                @endif

                {{ KJField::text('NUMBER_BANK', KJLocalization::translate('Admin - CRM', 'Bankrekeningnummer', 'Bankrekeningnummer'), $item ? $item->NUMBER_BANK : '', true, ['data-screen-mode' => 'read, edit']) }}
                {{ KJField::number('PAYMENTTERM_DAY', KJLocalization::translate('Admin - CRM', 'Betalingstermijn', 'Betalingstermijn'), $item ? $item->PAYMENTTERM_DAY : '', true, ['data-screen-mode' => 'read, edit', 'step' => 1, 'min' => 1]) }}
            </div>

            <div class="col-xl-4 col-lg-6">
                {{ KJField::checkbox('INVOICE_ELECTRONIC', KJLocalization::translate('Admin - CRM', 'Digitaal factureren', 'Digitaal factureren'), true, $item ? $item->INVOICE_ELECTRONIC : '', ['data-screen-mode' => 'read, edit']) }}
            </div>
        </div>

        @if(!$item)
            {{ KJField::saveCancel(
                'btnSaveFinancialDetailsNew',
                'btnCancelFinancialDetailsNew',
                true,
                array(
                    'saveText' => KJLocalization::translate('Algemeen', 'Toevoegen', 'Toevoegen'), 'saveStyle' => 'btn-success',
                    'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren'), 'cancelStyle' => 'btn-secondary'
                )
            ) }}
        @else
            {{ KJField::saveCancel(
                'btnSaveFinancialDetails',
                'btnCancelFinancialDetails',
                true,
                array(
                    'saveText' => KJLocalization::translate('Algemeen', 'Opslaan', 'Opslaan'),
                    'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren')
                )
            ) }}
        @endif
    {{ Form::close() }}
</div>
