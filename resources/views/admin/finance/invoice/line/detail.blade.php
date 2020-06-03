{{ Form::open(array(
    'method' => 'post',
    'id' => 'invoice_line',
    'class' => 'kt-form',
    'novalidate'
)) }}
<div class="kt-portlet__body kt-portlet__body--fit-inline-datatable">
    {{ Form::hidden('ID', $item ? $item->ID : -1) }}
    {{ Form::hidden('QUANTITY_MONTH', $item ? $item->QUANTITY_MONTH : 1) }}

    <div class="row">
        <div class="col-xl-4 col-lg-6">
            {{--Required--}} {{ KJField::number('QUANTITY', KJLocalization::translate('Admin - Facturen', 'Aantal', 'Aantal'), $item ? $item->QuantityDecimal : '', true, ['required']) }}
            {{--Required--}} {{ KJField::text('DESCRIPTION', KJLocalization::translate('Admin - Facturen', 'Omschrijving', 'Omschrijving'), $item ? $item->DESCRIPTION : '', true, ['required'], [
                'right' => [
                    ['type' => 'hidden', 'name' => 'FK_ASSORTMENT_PRODUCT', 'value' => $item->FK_ASSORTMENT_PRODUCT ?? ''],
                    ['type' => 'button', 'caption' => KJLocalization::translate('Admin - Facturen', 'Interventie', 'Interventie'), 'class' => 'btn btn-primary btn-sm selectProduct']
                ]
            ]) }}
            {{--Required--}} {{ KJField::number('PRICE', KJLocalization::translate('Admin - Facturen', 'Verkoopprijs', 'Verkoopprijs'), $item->PriceDecimal ?? '' , true, ['required', 'step' => '.01'],['right'=>['&euro;']]) }}
            {{--Required--}} {{ KJField::select('FK_FINANCE_LEDGER', KJLocalization::translate('Admin - Facturen', 'Omzet grootboekrekening', 'Omzet grootboekrekening'), $ledgers, $item ? $item->FK_FINANCE_LEDGER : 0, true, 0, ['required', 'data-live-search' => 1, 'data-size' => 5]) }}
            {{--Required--}} {{ KJField::select('FK_FINANCE_VAT', KJLocalization::translate('Admin - Facturen', 'Btw', 'Btw'), $vat, $item ? $item->FK_FINANCE_VAT : '', true, 0, ['required', 'data-live-search' => 1, 'data-size' => 5]) }}
        </div>
    </div>

    @if(!$item)
        {{ KJField::saveCancel(
            'btnSaveInvoiceItemNew',
            'btnCancelInvoiceItemNew',
            true,
            array(
                'saveText' => KJLocalization::translate('Algemeen', 'Toevoegen', 'Toevoegen'), 'saveStyle' => 'btn-success',
                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren'), 'cancelStyle' => 'btn-secondary'
            )
        ) }}
    @else
        {{ KJField::saveCancel(
            'btnSaveInvoiceItem',
            'btnCancelInvoiceItem',
            true,
            array(
                'saveText' => KJLocalization::translate('Algemeen', 'Opslaan', 'Opslaan'),
                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren')
            )
        ) }}
    @endif
</div>
{{ Form::close() }}