{{ Form::open(array(
    'method' => 'post',
    'id' => 'detailFormAddress',
    'class' => 'kt-form',
    'novalidate'
)) }}
<div class="kt-portlet__body kt-portlet__body--fit-inline-datatable">
    {{ Form::hidden('ID', $item ? $item->ID : -1) }}

    <div class="row">
        <div class="col-xl-6 col-lg-6">
            {{-- Required --}} {{ KJField::select('FK_CORE_DROPDOWNVALUE_ADRESSTYPE', KJLocalization::translate('Admin - CRM', 'Adrestype', 'Adrestype'), $addresstypes, $item ? $item->FK_CORE_DROPDOWNVALUE_ADRESSTYPE : '', true, 0, ['required']) }}
            {{-- Required --}} {{ KJField::select('FK_CORE_COUNTRY', KJLocalization::translate('Admin - CRM', 'Land', 'Land'), $countries, $item ? $item->address->FK_CORE_COUNTRY : 1, true, 0, ['required', 'data-live-search' => 1]) }}

            {{ KJField::postcodelookup('ZIPCODE', KJLocalization::translate('Admin - CRM', 'Postcode en huisnummer', 'Postcode en huisnummer'), $item ? $item->address->ZIPCODE : '', [
                'data-country' => ($item->address->country->COUNTRYCODE ?? 'NL'),
                'housenumber' => [
                    'name' => 'HOUSENUMBER',
                    'postcodecaption' => KJLocalization::translate('Admin - CRM', 'Postcode', 'Postcode'),
                    'housenumbercaption' => KJLocalization::translate('Admin - CRM', 'Huisnummer', 'Huisnummer'),
                    'value' => $item ? $item->address->HOUSENUMBER : ''
                ]
            ], 'ADDRESSLINE', 'CITY') }}
            {{-- Required --}} {{ KJField::text('ADDRESSLINE', KJLocalization::translate('Admin - CRM', 'Adresregel', 'Adresregel'), $item ? $item->address->ADDRESSLINE : '', true, ['required']) }}
            {{-- Required --}} {{ KJField::text('CITY', KJLocalization::translate('Admin - CRM', 'Stad', 'Stad'), $item ? $item->address->CITY : '', true, ['required']) }}
        </div>
    </div>

    @if(!$item)
        {{ KJField::saveCancel(
            'btnSaveAddressNew',
            'btnCancelAddressNew',
            true,
            array(
                'saveText' => KJLocalization::translate('Algemeen', 'Toevoegen', 'Toevoegen'), 'saveStyle' => 'btn-success',
                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren'), 'cancelStyle' => 'btn-secondary'
            )
        ) }}
    @else
        {{ KJField::saveCancel(
            'btnSaveAddress',
            'btnCancelAddress',
            true,
            array(
                'saveText' => KJLocalization::translate('Algemeen', 'Opslaan', 'Opslaan')
            )
        ) }}
    @endif
</div>
{{ Form::close() }}