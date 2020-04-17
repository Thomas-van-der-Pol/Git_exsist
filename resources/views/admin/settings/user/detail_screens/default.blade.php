<div class="kt-widget__top">
    <div class="kt-widget__content">
        <div class="kt-widget__head">
            <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/user') }}" class="back-button"></a>
            <h4>{{ ($item ? $item->title : KJLocalization::translate('Admin - Werknemers', 'Nieuwe werknemer', 'Nieuwe werknemer')) }} <button type="button" class="btn btn-sm btn-icon setEditMode" data-target="default"><i class="fa fa-pen"></i></button></h4>

            <div class="kt-widget__action">
                @if($item)
                    <button type="button" class="btn btn-brand btn-sm btn-upper resetPassword kt-margin-b-5-tablet-and-mobile" data-id="{{ $item->ID }}" data-email="{{ $item ? $item->EMAILADDRESS : '' }}">{{ KJLocalization::translate('Admin - Werknemers', 'Wachtwoord resetten', 'Wachtwoord resetten') }}</button>
                    <button type="button" class="btn btn-brand btn-sm btn-upper anonymizeUser kt-margin-b-5-tablet-and-mobile" data-id="{{ $item->ID }}">{{ KJLocalization::translate('Admin - Werknemers', 'Gegevens anonimiseren', 'Gegevens anonimiseren') }}</button>

                    @if($item->ACTIVE ?? true)
                        <button type="button" class="btn btn-danger btn-sm btn-upper activateItem" data-id="{{ $item->ID }}">{{ KJLocalization::translate('Algemeen', 'Archiveren', 'Archiveren') }}</button>
                    @else
                        <button type="button" class="btn btn-success btn-sm btn-upper activateItem" data-id="{{ $item->ID }}">{{ KJLocalization::translate('Algemeen', 'Activeren', 'Activeren') }}</button>
                    @endif
                @endif
            </div>
        </div>

        <div class="kt-widget__info mt-3">
            <div class="kt-widget__desc">
                {{ Form::open(array(
                    'method' => 'post',
                    'id' => 'user_default',
                    'class' => 'kt-form',
                    'novalidate'
                )) }}
                {{ Form::hidden('ID', $item ? $item->ID : -1) }}

                <div class="row">
                    <div class="col-xl-8 col-lg-12">
                        <div class="row">
                            <div class="col-6">
                                {{ KJField::text('USERCODE', KJLocalization::translate('Admin - Werknemers', 'Personeelsnummer', 'Personeelsnummer'), $item ? $item->USERCODE : '', true, ['required', 'data-screen-mode' => 'read, edit']) }}

                                {{ KJField::text('FIRSTNAME', KJLocalization::translate('Admin - Werknemers', 'Voornaam', 'Voornaam'), $item ? $item->FIRSTNAME : '', true, ['required', 'data-screen-mode' => 'edit']) }}
                                {{ KJField::text('PREPOSITION', KJLocalization::translate('Admin - Werknemers', 'Tussenvoegsel', 'Tussenvoegsel'), $item ? $item->PREPOSITION : '', true, ['data-screen-mode' => 'edit']) }}
                                {{ KJField::text('LASTNAME', KJLocalization::translate('Admin - Werknemers', 'Achternaam', 'Achternaam'), $item ? $item->LASTNAME : '', true, ['required', 'data-screen-mode' => 'edit']) }}

                                {{ KJField::email('EMAILADDRESS', KJLocalization::translate('Admin - Werknemers', 'E-mailadres', 'E-mailadres'), $item ? $item->EMAILADDRESS : '', ['required','data-screen-mode' => 'read, edit']) }}
                            </div>

                            <div class="col-6">
                                {{ KJField::text('PHONE', KJLocalization::translate('Admin - Werknemers', 'Telefoon', 'Telefoon'), $item ? $item->PHONE : '', true, ['data-screen-mode' => 'read, edit']) }}
                                {{ KJField::text('PHONE_MOBILE', KJLocalization::translate('Admin - Werknemers', 'Mobiel', 'Mobiel'), $item ? $item->PHONE_MOBILE : '', true, ['data-screen-mode' => 'read, edit']) }}
                                {{ KJField::text('PHONE_EMERGENCY', KJLocalization::translate('Admin - Werknemers', 'Telefoon noodgeval', 'Telefoon noodgeval'), $item ? $item->PHONE_EMERGENCY : '', true, ['data-screen-mode' => 'read-hide-empty, edit']) }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                @component('moreless::main', ['colsize' => 12, 'spacesize' => 0])
                                    <div class="row mt-4">
                                        <div class="col">
                                            <h5 class="mb-3">{{ KJLocalization::translate('Algemeen', 'Aanvullende gegevens', 'Aanvullende gegevens') }}</h5>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            {{-- ADRES WEERGAVE --}}
                                            <div class="md-form">
                                                <p data-screen-mode="read">{!!  ($item && $item->address) ? nl2br($item->address->fullAddress()) : ''  !!}</p>
                                            </div>

                                            {{-- ADRES BEWERKING --}}
                                            {{ KJField::postcodelookup('ADDRESS_ZIPCODE', KJLocalization::translate('Admin - Werknemers', 'Postcode', 'Postcode'), ($item && $item->address) ? $item->address->ZIPCODE : '', [
                                                'data-country' => ( ($item && $item->address) ? $item->address->country->COUNTRYCODE : 'NL'),
                                                'data-screen-mode' => 'edit',
                                                'housenumber' => [
                                                    'name' => 'ADDRESS_HOUSENUMBER',
                                                    'postcodecaption' => KJLocalization::translate('Admin - Werknemers', 'Postcode', 'Postcode'),
                                                    'housenumbercaption' => KJLocalization::translate('Admin - Werknemers', 'Huisnummer', 'Huisnummer'),
                                                    'value' => ($item && $item->address) ? $item->address->HOUSENUMBER : ''
                                                ]
                                            ], 'ADDRESS_ADDRESSLINE', 'ADDRESS_CITY') }}

                                            {{ KJField::text('ADDRESS_ADDRESSLINE', KJLocalization::translate('Admin - Werknemers', 'Adresregel', 'Adresregel'), ($item && $item->address) ? $item->address->ADDRESSLINE : '', true, ['data-screen-mode' => 'edit']) }}
                                            {{ KJField::text('ADDRESS_CITY', KJLocalization::translate('Admin - Werknemers', 'Woonplaats', 'Woonplaats'), ($item && $item->address) ? $item->address->CITY : '', true, ['data-screen-mode' => 'edit']) }}
                                            {{ KJField::select('ADDRESS_FK_CORE_COUNTRY', KJLocalization::translate('Admin - Werknemers', 'Land', 'Land'), $countries, ($item && $item->address) ? $item->address->FK_CORE_COUNTRY : 1, true, 0, ['data-screen-mode' => 'edit', 'data-live-search' => 1]) }}
                                        </div>

                                        <div class="col-6">
                                            {{ KJField::select('FK_CORE_DROPDOWNVALUE_GENDER', KJLocalization::translate('Admin - Werknemers', 'Geslacht', 'Geslacht'), $genders, $item ? $item->FK_CORE_DROPDOWNVALUE_GENDER : '', true, config('dropdown_type.TYPE_GENDER'), ['data-screen-mode' => 'read, edit']) }}
                                            {{ KJField::date('DATE_OF_BIRTH', KJLocalization::translate('Admin - Werknemers', 'Geboortedatum', 'Geboortedatum'), $item ? $item->getDateOfBirthFormattedAttribute() : '', ['data-screen-mode' => 'read, edit', 'data-date-end-date' => '-0d', 'data-locale-format' => \KJ\Localization\libraries\LanguageUtils::getJSDatePickerFormat()]) }}
                                            {{ KJField::checkbox('RECEIVE_NOTIFICATION', KJLocalization::translate('Admin - Werknemers', 'Notificatie verjaardag ontvangen', 'Notificatie verjaardag ontvangen'), true, ($item ? $item->RECEIVE_NOTIFICATION : false), true) }}
                                            <div class="RECEIVE_NOTIFICATION_SETTING {{ ( $item && ($item->GET_NOTIFICATION == true) ) ? '' : 'kt-hide' }}">
                                                {{ KJField::select('FK_CORE_ROLE_NOTIFICATION', KJLocalization::translate('Admin - Werknemers', 'Notificatie rol', 'Notificatie rol'), $roles, ($item ? $item->FK_CORE_ROLE_NOTIFICATION : ''), true, 0) }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col">
                                            <h5 class="mb-3">{{ KJLocalization::translate('Admin - Werknemers', 'Administratieve gegevens', 'Administratieve gegevens') }}</h5>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            {{ KJField::select('FK_CORE_DROPDOWNVALUE_DEPARTMENT', KJLocalization::translate('Admin - Werknemers', 'Afdeling', 'Afdeling'), $departments, $item ? $item->FK_CORE_DROPDOWNVALUE_DEPARTMENT : '', true, config('dropdown_type.TYPE_DEPARTMENTS'), ['data-screen-mode' => 'read, edit']) }}
                                            {{ KJField::select('FK_CORE_DROPDOWNVALUE_POSITION', KJLocalization::translate('Admin - Werknemers', 'Functie', 'Functie'), $positions, $item ? $item->FK_CORE_DROPDOWNVALUE_POSITION : '', true, config('dropdown_type.TYPE_POSITIONS'), ['data-screen-mode' => 'read, edit']) }}

                                            {{ KJField::checkbox('EXCLUDE_PAYMENT', KJLocalization::translate('Admin - Werknemers', 'Uitsluiten van verloning', 'Uitsluiten van verloning'), true, $item ? $item->EXCLUDE_PAYMENT : false, true, ['data-screen-mode' => 'read, edit']) }}
                                            {{ KJField::checkbox('ZZP', KJLocalization::translate('Admin - Werknemers', 'ZZPer', 'ZZP\'er'), true, $item ? $item->ZZP : false, true, ['data-screen-mode' => 'read, edit']) }}
                                        </div>

                                        <div class="col-6">
                                            {{ KJField::textarea('REMARKS', KJLocalization::translate('Admin - Werknemers', 'Opmerkingen', 'Opmerkingen'), $item ? $item->REMARKS : '', 6, true, ['data-screen-mode' => 'read, edit']) }}
                                        </div>
                                    </div>
                                @endcomponent
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-6">
                        <div class="md-form mt-3">
                            <div class="form-group">
                                <div class="kt-avatar" id="LOGO_USER_SELECT">
                                    <div class="kt-avatar__holder" style="width: 300px; background-image: url({{ asset($item ? ((($item->PHOTO ?? '') != '') ? config('app.cdn_url') . $item->PHOTO : '/assets/theme/img/noimage_thumbnail.png') : '/assets/theme/img/noimage_thumbnail.png') }})"></div>
                                    <label class="kt-avatar__upload default-label" data-toggle="kt-tooltip" title="" data-original-title="{{ KJLocalization::translate('Algemeen', 'Afbeelding veranderen', 'Afbeelding veranderen') }}">
                                        <i class="fa fa-pen"></i>
                                        <input type="file" name="PHOTO" tabindex="-1">
                                    </label>
                                    <span class="kt-avatar__cancel" data-toggle="kt-tooltip" title="" data-original-title="{{ KJLocalization::translate('Algemeen', 'Afbeelding herstellen', 'Afbeelding herstellen') }}">
                                        <i class="fa fa-times"></i>
                                    </span>
                                </div>
                                <span class="form-text text-muted">{{ KJLocalization::translate('Algemeen', 'Afbeelding bestanden', 'Afbeelding bestanden') }}: *.jpg, *.jpeg, *.png</span>
                                <label class="active" style="top: -14px;">{{ KJLocalization::translate('Admin - Werknemers', 'Logo werknemer', 'Logo werknemer') }}</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        @if(!$item)
                            {{ KJField::saveCancel(
                                'btnSaveUserNew',
                                'btnCancelUserNew',
                                true,
                                array(
                                    'saveText' => KJLocalization::translate('Algemeen', 'Toevoegen', 'Toevoegen'), 'saveStyle' => 'btn-success',
                                    'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren'), 'cancelStyle' => 'btn-secondary'
                                )
                            ) }}
                        @else
                            {{ KJField::saveCancel(
                                'btnSaveUser',
                                'btnCancelUser',
                                true,
                                array(
                                    'saveText' => KJLocalization::translate('Algemeen', 'Opslaan', 'Opslaan'),
                                    'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren')
                                )
                            ) }}
                        @endif
                    </div>
                </div>

                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>