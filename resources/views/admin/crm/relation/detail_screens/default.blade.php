<div class="kt-widget__top">
    <div class="kt-widget__content">
        <div class="kt-widget__head">
            <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/crm/relation') }}" class="back-button"></a>
            <h4>{{ ($item ? $item->title : KJLocalization::translate('Admin - CRM', 'Nieuwe relatie', 'Nieuwe relatie')) }} <button type="button" class="btn btn-sm btn-icon setEditMode" data-target="default"><i class="fa fa-pen"></i></button></h4>

            <div class="kt-widget__action">
                @if($item)
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
                    'id' => 'relation_default',
                    'class' => 'kt-form',
                    'novalidate'
                )) }}
                    {{ Form::hidden('ID', $item ? $item->ID : -1) }}
                    @if($default_label > 0)
                        {{ Form::hidden('FK_CORE_LABEL', $item ? $item->FK_CORE_LABEL : $default_label) }}
                    @endif

                    <div class="row">
                        <div class="col-xl-4 col-lg-6">
                            {{--Required--}} {{ KJField::text('NAME', KJLocalization::translate('Admin - CRM', 'Naam', 'Naam'), $item ? $item->NAME : '', true, ['required', 'data-screen-mode' => 'edit']) }}
                            {{ KJField::text('PHONENUMBER', KJLocalization::translate('Admin - CRM', 'Telefoonnummer', 'Telefoonnummer'), $item ? $item->PHONENUMBER : '', true, ['data-screen-mode' => 'read, edit']) }}
                            {{ KJField::email('EMAILADDRESS', KJLocalization::translate('Admin - CRM', 'E-mailadres', 'E-mailadres'), $item ? $item->EMAILADDRESS : '', ['data-screen-mode' => 'read, edit']) }}
                            {{ KJField::text('WEBSITE', KJLocalization::translate('Admin - CRM', 'Website', 'Website'), $item ? $item->WEBSITE : '', true, ['data-screen-mode' => 'read, edit']) }}
                        </div>

                        <div class="col-xl-4 col-lg-6">
                            {{ KJField::select('FK_CORE_DROPDOWNVALUE_RELATIONTYPE', KJLocalization::translate('Admin - CRM', 'Type relatie', 'Type relatie'), $relationtypes, $item ? $item->FK_CORE_DROPDOWNVALUE_RELATIONTYPE : '', true, 0, ['required', 'data-screen-mode' => 'read, edit']) }}
                            {{ KJField::textarea('REMARKS', KJLocalization::translate('Admin - CRM', 'Opmerkingen', 'Opmerkingen'), $item ? $item->REMARKS : '', 3, true, ['data-screen-mode' => 'read, edit'] ) }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            @component('moreless::main', ['colsize' => 12, 'spacesize' => 0])
                                <div class="row mt-4">
                                    <div class="col">
                                        <h5 class="mb-3">{{ KJLocalization::translate('Admin - CRM', 'Administratieve gegevens', 'Administratieve gegevens') }}</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xl-4 col-lg-6">
                                        @if($item && ($item->contacts->where('ACTIVE', true)->count() > 0))
                                            {{ KJField::select('FK_CRM_CONTACT_FINANCE', KJLocalization::translate('Admin - CRM', 'Financieel contact', 'Financieel contact'), $contactpersonen, $item ? $item->FK_CRM_CONTACT_FINANCE : 0, true, -1, ['data-screen-mode' => 'read, edit']) }}
                                        @else
                                            {{KJLocalization::translate('Admin - CRM', 'Voeg eerst contactpersonen toe', 'Voeg eerst contactpersonen toe')}}
                                        @endif
                                    </div>

                                    <div class="col-xl-4 col-lg-6">
                                        @if($default_label == null)
                                            {{ KJField::select('FK_CORE_LABEL', KJLocalization::translate('Admin - CRM', 'Administratie', 'Administratie'), $labels, $item ? $item->FK_CORE_LABEL : 0, true, -1, ['data-screen-mode' => 'read, edit']) }}
                                        @endif
                                        {{ KJField::checkbox('SINGED_DATAPROCESSINGAGREEMENT', KJLocalization::translate('Admin - CRM', 'Verwerkingsovereenkomst getekend', 'Verwerkingsovereenkomst getekend'), true, ( $item ? $item->SINGED_DATAPROCESSINGAGREEMENT : 0 ), ['data-screen-mode' => 'read, edit']) }}
                                        {{ KJField::checkbox('VAT_LIABLE', KJLocalization::translate('Admin - CRM', 'Btw-plichtig', 'Btw-plichtig'), true, ( $item ? $item->VAT_LIABLE : true ), ['data-screen-mode' => 'read, edit']) }}
                                    </div>
                                </div>
                            @endcomponent
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            @if(!$item)
                                {{ KJField::saveCancel(
                                    'btnSaveRelationNew',
                                    'btnCancelRelationNew',
                                    true,
                                    array(
                                        'saveText' => KJLocalization::translate('Algemeen', 'Toevoegen', 'Toevoegen'), 'saveStyle' => 'btn-success',
                                        'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren'), 'cancelStyle' => 'btn-secondary'
                                    )
                                ) }}
                            @else
                                {{ KJField::saveCancel(
                                    'btnSaveRelation',
                                    'btnCancelRelation',
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