{{ Form::open(array(
    'method' => 'post',
    'id' => 'detailFormProductInvoiceScheme',
    'class' => 'kt-form',
)) }}
<div class="kt-portlet__body kt-portlet__body--fit-inline-datatable">
    {{ Form::hidden('ID', $item ? $item->ID : -1) }}
    <div class='col-lg-6'>
    {{--Required--}} {{ KJField::date('DATE', KJLocalization::translate('Admin - Dossiers', 'Datum', 'Datum'), (date(KJ\Localization\libraries\LanguageUtils::getDateFormat(), strtotime(date("D M d")))), ['required', 'data-date-start-date' => '-0d', 'data-locale-format' => \KJ\Localization\libraries\LanguageUtils::getJSDatePickerFormat()]) }}
{{--    --}}{{--Required--}}{{-- {{ KJField::date('DATE',KJLocalization::translate('Admin - Dossiers', 'Dagen tellen tot facturatie datum', 'Dagen tellen tot facturatie datum'), $item ? $item->DAYS : 0, true, ['required', 'min' => 0, 'steps' => 1]) }}--}}
    {{--Required--}} {{ KJField::number('PERCENTAGE',KJLocalization::translate('Admin - Dossiers', 'Percentage', 'Percentage'), $item ? number_format($item->PERCENTAGE,2) : 0, true, ['required', 'max'=> 100, 'min' => 0, 'steps' => 1]) }}
        @if(!$item)
            {{ KJField::select('FK_ASSORTMENT_PRODUCT', KJLocalization::translate('Admin - Dossiers', 'Interventie', 'Interventie'), $products, ( $item ? $item->product->title : '' ), true, 0, ['required']) }}
        @else
            {{ KJField::text('FK_ASSORTMENT_PRODUCT', KJLocalization::translate('Admin - Dossiers', 'Interventie', 'Interventie'), ( $item ? $item->product->title : '' ), true, ['readonly', 'disabled']) }}
            {{ KJField::text('INTERVENTION_PRICE', KJLocalization::translate('Admin - Dossiers', 'Stukprijs interventie', 'Stukprijs interventie'), ( $item ? $item->product->getPriceFormattedAttribute() : '' ), true, ['readonly', 'disabled']) }}
            {{ KJField::text('SUBTOTAL_PERCENTAGE', KJLocalization::translate('Admin - Dossiers', 'Subtotaal', 'Subtotaal'), ( $item ? $item->getPricePercentageFormattedAttribute() : '' ), true, ['readonly', 'disabled']) }}
            {{ KJField::text('INVOICE_NUMBER', KJLocalization::translate('Admin - Dossiers', 'Factuurnummer', 'Factuurnummer'), ( $item ? ($item->invoiceLine? $item->invoiceLine->invoice->NUMBER : ''):'' ), true, ['readonly', 'disabled']) }}
        @endif

    </div>
    @if(!$item)
        {{ KJField::saveCancel(
            'btnSaveInvoiceSchemeNew',
            'btnCancelInvoiceSchemeNew',
            true,
            array(
                'saveText' => KJLocalization::translate('Algemeen', 'Toevoegen', 'Toevoegen'), 'saveStyle' => 'btn-success',
                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren'), 'cancelStyle' => 'btn-secondary'
            )
        ) }}
    @else
        {{ KJField::saveCancel(
            'btnSaveInvoiceScheme',
            'btnCancelInvoiceScheme',
            true,
            array(
                'saveText' => KJLocalization::translate('Algemeen', 'Opslaan', 'Opslaan'),
                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren')
            )
        ) }}
    @endif
</div>
{{ Form::close() }}