{{ Form::open(array(
    'method' => 'post',
    'id' => 'detailFormContact',
    'class' => 'kt-form',
    'novalidate'
)) }}
<div class="kt-portlet__body kt-portlet__body--fit-inline-datatable">
    {{ Form::hidden('ID', $item ? $item->ID : -1) }}

    <div class="row">
        <div class="col-xl-5 col-lg-7">
            @if($item)
                <div class="md-form">
                    <div class="form-group">
                        <a href="javascript:;" class="anonymizeContact" data-id="{{ $item->ID }}">
                            {{ KJLocalization::translate('Admin - CRM', 'Gegevens anonimiseren', 'Gegevens anonimiseren') }}
                        </a>
                    </div>
                </div>
            @endif

            {{--Required--}} {{ KJField::select('ACTIVE', KJLocalization::translate('Admin - CRM', 'Status', 'Status'), $status, $item ? $item->ACTIVE : true, true, 0, ['required']) }}
            {{ KJField::select('FK_CORE_DROPDOWNVALUE_GENDER', KJLocalization::translate('Admin - CRM', 'Geslacht', 'Geslacht'), $genders, $item ? $item->FK_CORE_DROPDOWNVALUE_GENDER : '', true, config('dropdown_type.TYPE_GENDER')) }}
            {{ KJField::select('FK_CORE_DROPDOWNVALUE_SALUTATION', KJLocalization::translate('Admin - CRM', 'Aanhef', 'Aanhef'), $salutions, $item ? $item->FK_CORE_DROPDOWNVALUE_SALUTATION : '', true, config('dropdown_type.TYPE_SALUTATION')) }}
            {{ KJField::select('FK_CORE_DROPDOWNVALUE_ATTN', KJLocalization::translate('Admin - CRM', 'T.a.v.', 'T.a.v.'), $attentionsto, $item ? $item->FK_CORE_DROPDOWNVALUE_ATTN : '', true, config('dropdown_type.TYPE_ATTN')) }}
            {{ KJField::text('INITIALS', KJLocalization::translate('Admin - CRM', 'Voorletters', 'Voorletters'), $item ? $item->INITIALS : '') }}
            {{ KJField::text('FIRSTNAME', KJLocalization::translate('Admin - CRM', 'Voornaam', 'Voornaam'), $item ? $item->FIRSTNAME : '') }}
            {{ KJField::text('PREPOSITION', KJLocalization::translate('Admin - CRM', 'Tussenvoegsel', 'Tussenvoegsel'), $item ? $item->PREPOSITION : '') }}
            {{--Required--}} {{ KJField::text('LASTNAME', KJLocalization::translate('Admin - CRM', 'Achternaam', 'Achternaam'), $item ? $item->LASTNAME : '', true, ['required']) }}
            {{ KJField::date('BIRTHDAY_DATE', KJLocalization::translate('Admin - Taken', 'Geboortedatum', 'Geboortedatum'), ($item ? $item->getBirthdayDateFormattedAttribute() : ''), ['data-date-end-date' => '-0d', 'data-locale-format' => \KJ\Localization\libraries\LanguageUtils::getJSDatePickerFormat()]) }}
            {{ KJField::text('PHONENUMBER', KJLocalization::translate('Admin - CRM', 'Telefoonnummer', 'Telefoonnummer'), $item ? $item->PHONENUMBER : '', true, ['pattern' => '^[0-9-+()\s]*$', 'title' => KJLocalization::translate('Algemeen', 'Gebruik alleen nummers en speciale tekens', 'Gebruik alleen nummers en speciale tekens')]) }}
            {{ KJField::text('CELLPHONENUMBER', KJLocalization::translate('Admin - CRM', 'Mobiel', 'Mobiel'), $item ? $item->CELLPHONENUMBER : '', true, ['pattern' => '^[0-9-+()\s]*$', 'title' => KJLocalization::translate('Algemeen', 'Gebruik alleen nummers en speciale tekens', 'Gebruik alleen nummers en speciale tekens')]) }}
            {{--Required--}} {{ KJField::email('EMAILADDRESS', KJLocalization::translate('Admin - CRM', 'E-mailadres', 'E-mailadres'), $item ? $item->EMAILADDRESS : '', ['required'], []) }}
            @if($item && $item->NEW_PASSWORD)
                <span class="form-text text-muted mb-2 pb-2">Verzonden op: {{ $item ? date('d-m-Y H:i', strtotime($item->NEW_PASSWORD)) : '' }}</span>
            @endif

            {{ KJField::textarea('REMARKS', KJLocalization::translate('Admin - CRM', 'Opmerkingen', 'Opmerkingen'), $item ? $item->REMARKS : '', 2) }}
        </div>
    </div>

    @if(!$item)
        {{ KJField::saveCancel(
            'btnSaveContactNew',
            'btnCancelContactNew',
            true,
            array(
                'saveText' => KJLocalization::translate('Algemeen', 'Toevoegen', 'Toevoegen'), 'saveStyle' => 'btn-success',
                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren'), 'cancelStyle' => 'btn-secondary'
            )
        ) }}
    @else
        {{ KJField::saveCancel(
            'btnSaveContact',
            'btnCancelContact',
            true,
            array(
                'saveText' => KJLocalization::translate('Algemeen', 'Opslaan', 'Opslaan'),
                'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren')
            )
        ) }}
    @endif
</div>
{{ Form::close() }}