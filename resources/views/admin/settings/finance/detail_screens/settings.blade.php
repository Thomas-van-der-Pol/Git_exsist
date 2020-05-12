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
                    {{ KJLocalization::translate('Admin - Financieel', 'Volmacht', 'Volmacht') }}
                    <button type="button" class="btn btn-sm btn-icon setEditMode" data-target="settings">
                        <i class="fa fa-pen"></i>
                    </button>
                </h5>
            </div>
        </div>
    
        <div class="row">
            <div class="col-xl-4 col-lg-4">
                @php
                    $relationButtons = [];
                    $relationButtons[] = ['type' => 'button', 'caption' => KJLocalization::translate('Admin - Financieel', 'Relatie', 'Relatie'), 'class' => 'btn btn-primary btn-sm selectRelation'];
                    if (Auth::guard()->user()->hasPermission(config('permission.CRM'))) {
                        $relationButtons[] = ['type' => 'button', 'caption' => KJLocalization::translate('Algemeen', 'Openen', 'Openen'), 'class' => 'btn btn-dark btn-sm openRelation'];
                    }
                @endphp

                {{--Required--}} {{ KJField::text('PROXY_NAME', KJLocalization::translate('Admin - Financieel', 'Volmachtbedrijf tbv vergoedingen', 'Volmachtbedrijf t.b.v. vergoedingen'), $item ? ($item->proxy ? $item->proxy->title : '-') : '-', true, ['readonly', 'required', 'data-screen-mode' => 'read, edit'], [
                     'right' => $relationButtons
                ]) }}
                {{ Form::hidden('FK_CRM_RELATION_PROXY', $item ? $item->FK_CRM_RELATION_PROXY : null, ['required']) }}
            </div>
        </div>

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
                {{ KJField::text('NEXT_CREDITOR_NUMBER', KJLocalization::translate('Admin - Financieel', 'Volgende crediteurnummer', 'Volgende crediteurnummer'), $item ? $item->NEXT_CREDITOR_NUMBER : '', true, ['data-screen-mode' => 'read, edit']) }}
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
                {{ KJField::select('FK_FINANCE_LEDGER_DEFAULT_COMPENSATION', KJLocalization::translate('Admin - Financieel', 'Grootboekrekening tbv vergoeding', 'Grootboekrekening t.b.v. vergoeding'), $ledgers, ( $item ? $item->FK_FINANCE_LEDGER_DEFAULT_COMPENSATION : '' ), true, 0, ['data-screen-mode' => 'read, edit']) }}
                {{ KJField::select('FK_FINANCE_VAT_DEFAULT_COMPENSATION', KJLocalization::translate('Admin - Financieel', 'Btw tbv vergoeding', 'Btw t.b.v. vergoeding'), $vat, ( $item ? $item->FK_FINANCE_VAT_DEFAULT_COMPENSATION : '' ), true, 0, ['data-screen-mode' => 'read, edit']) }}
                {{ KJField::select('FK_FINANCE_VAT_SHIFTED', KJLocalization::translate('Admin - Financieel', 'Btw verlegd', 'Btw verlegd'), $vat, ( $item ? $item->FK_FINANCE_VAT_SHIFTED : '' ), true, 0, ['data-screen-mode' => 'read, edit']) }}
            </div>

            <div class="col-xl-4 col-lg-4">
                {{ KJField::number('DEFAULT_PAYMENTTERM_DAY', KJLocalization::translate('Admin - Financieel', 'Betalingstermijn', 'Betalingstermijn'), $item ? $item->DEFAULT_PAYMENTTERM_DAY : '', true, ['data-screen-mode' => 'read, edit']) }}
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