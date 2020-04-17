<div class="kt-portlet__body">
    {{ Form::open(array(
        'method' => 'post',
        'id' => 'label_settings',
        'class' => 'kt-form',
        'novalidate'
    )) }}
    {{ Form::hidden('ID', $item ? $item->ID : -1) }}

        <div class="row">
            <div class="col">
                <h5 class="mb-3">
                    {{ KJLocalization::translate('Admin - Financieel', 'Nummering', 'Nummering') }}
                    <button type="button" class="btn btn-sm btn-icon setEditMode" data-target="settings">
                        <i class="fa fa-pen"></i>
                    </button>
                </h5>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-4 col-lg-4">
                {{ KJField::text('NEXT_INVOICE_NUMBER', KJLocalization::translate('Admin - Financieel', 'Volgende factuurnummer', 'Volgende factuurnummer'), $item ? $item->NEXT_INVOICE_NUMBER : '', true, ['data-screen-mode' => 'read, edit']) }}
                {{ KJField::text('NEXT_DEBTOR_NUMBER', KJLocalization::translate('Admin - Financieel', 'Volgende debiteurnummer', 'Volgende debiteurnummer'), $item ? $item->NEXT_DEBTOR_NUMBER : '', true, ['data-screen-mode' => 'read, edit']) }}
            </div>
        </div>

        <div class="row">
            <div class="col">
                <h5 class="mb-3">
                    {{ KJLocalization::translate('Admin - Financieel', 'Standaarden', 'Standaarden') }}
                    <button type="button" class="btn btn-sm btn-icon setEditMode" data-target="settings">
                        <i class="fa fa-pen"></i>
                    </button>
                </h5>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-4 col-lg-4">
                {{ KJField::select('FK_FINANCE_LEDGER_DEFAULT_PROJECT', KJLocalization::translate('Admin - Financieel', 'Grootboekrekening tav aangenomen projecten', 'Grootboekrekening tav aangenomen projecten'), $ledgers, ( $item ? $item->FK_FINANCE_LEDGER_DEFAULT_PROJECT : '' ), true, 0, ['data-screen-mode' => 'read, edit']) }}
                {{ KJField::select('FK_FINANCE_VAT_DEFAULT_PROJECT', KJLocalization::translate('Admin - Financieel', 'Btw tav aangenomen projecten', 'Btw tav aangenomen projecten'), $vat, ( $item ? $item->FK_FINANCE_VAT_DEFAULT_PROJECT : '' ), true, 0, ['data-screen-mode' => 'read, edit']) }}
                {{ KJField::select('FK_FINANCE_VAT_SHIFTED', KJLocalization::translate('Admin - Financieel', 'Btw verlegd', 'Btw verlegd'), $vat, ( $item ? $item->FK_FINANCE_VAT_SHIFTED : '' ), true, 0, ['data-screen-mode' => 'read, edit']) }}
            </div>

            <div class="col-xl-4 col-lg-4">
                {{ KJField::number('DEFAULT_PAYMENTTERM_DAY', KJLocalization::translate('Admin - Financieel', 'Betalingstermijn', 'Betalingstermijn'), $item ? $item->DEFAULT_PAYMENTTERM_DAY : '', true, ['data-screen-mode' => 'read, edit']) }}

                {{--{{ KJField::text('DEFAULT_RATE_KM_READ', KJLocalization::translate('Admin - Financieel', 'Tarief per km', 'Tarief per km'), $item ? $item->getDefaultRateKmFormattedAttribute() : '', true, ['data-screen-mode' => 'read']) }}--}}
                {{--{{ KJField::number('DEFAULT_RATE_KM', KJLocalization::translate('Admin - Financieel', 'Tarief per km', 'Tarief per km'), $item ? number_format((float)$item->DEFAULT_RATE_KM,2, '.', '') : '', true, ['data-screen-mode' => 'edit'], [--}}
                    {{--'right' => [--}}
                        {{--['type' => 'text', 'caption' => '&euro;']--}}
                    {{--]--}}
                {{--]) }}--}}
                {{ KJField::checkbox('DEFAULT_DIGITAL_INVOICE', KJLocalization::translate('Algemeen', 'Digitaal factureren', 'Digitaal factureren'), true, ( $item ? $item->DEFAULT_DIGITAL_INVOICE : 0 ), true, ['data-screen-mode' => 'read, edit']) }}
            </div>
        </div>

        {{ KJField::saveCancel(
            'btnSaveLabelSettings',
            'btnCancelLabelSettings',
            true,
            array(
                'saveText' => KJLocalization::translate('Algemeen', 'Opslaan', 'Opslaan'),
                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren')
            )
        ) }}
    {{ Form::close() }}
</div>