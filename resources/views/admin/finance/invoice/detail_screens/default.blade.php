<div class="kt-widget__top">
    <div class="kt-widget__content">
        <div class="kt-widget__head">
            <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/invoice') }}" class="back-button"></a>
            <h4>
                {{ ($item ? $item->title : KJLocalization::translate('Admin - Facturen', 'Nieuwe factuur', 'Nieuwe factuur')) }}
                <button type="button" class="btn btn-sm btn-icon setEditMode" data-target="default"><i class="fa fa-pen"></i></button>
            </h4>

            <div class="kt-widget__action">
                @if($item)
                    {{-- ACTIONS --}}
                    <div class="btn-group btn-group" role="group">
                        @if(($item->FK_CORE_WORKFLOWSTATE ?? 0) == config('workflowstate.INVOICE_CONCEPT'))
                            <button type="button" class="btn btn-group-item btn-secondary btn-sm btn-upper kt-margin-b-5-tablet-and-mobile" data-id="{{ $item->ID }}" onclick="openKJPopup('/admin/invoice/previewPDF/{{ $item->ID }}#zoom=120')">{{ KJLocalization::translate('Admin - Facturen', 'Genereer conceptfactuur', 'Genereer conceptfactuur') }}</button>
                            <button type="button" class="btn btn-group-item btn-success btn-sm btn-upper sendInvoice kt-margin-b-5-tablet-and-mobile" data-id="{{ $item->ID }}">{{ KJLocalization::translate('Admin - Facturen', 'Factuur definitief versturen', 'Factuur definitief versturen') }}</button>
                        @elseif(($item->FK_CORE_WORKFLOWSTATE ?? 0) == config('workflowstate.INVOICE_FINAL'))
                            <button type="button" class="btn btn-group-item btn-secondary btn-sm btn-upper kt-margin-b-5-tablet-and-mobile" data-id="{{ $item->ID }}" onclick="openKJPopup('/admin/invoice/previewPDF/{{ $item->ID }}#zoom=120')">{{ KJLocalization::translate('Admin - Facturen', 'Bekijk factuur', 'Bekijk factuur') }}</button>
                            <button type="button" class="btn btn-group-item btn-brand btn-sm btn-upper sendInvoice kt-margin-b-5-tablet-and-mobile" data-id="{{ $item->ID }}">{{ KJLocalization::translate('Admin - Facturen', 'Factuur opnieuw mailen', 'Factuur opnieuw mailen') }}</button>

                            @if(!$item->PAID)
                                <button type="button" class="btn btn-group-item btn-warning btn-sm btn-upper sendInvoiceReminder kt-margin-b-5-tablet-and-mobile" data-id="{{ $item->ID }}">{{ KJLocalization::translate('Admin - Facturen', 'Herinnering versturen', 'Herinnering versturen') }}</button>
                            @endif
                        @endif
                    </div>

                    @if(($item->FK_CORE_WORKFLOWSTATE ?? 0) == config('workflowstate.INVOICE_CONCEPT'))
                        <button type="button" class="btn btn-danger btn-sm btn-upper deleteInvoice" data-id="{{ $item->ID }}">{{ KJLocalization::translate('Algemeen', 'Verwijderen', 'Verwijderen') }}</button>
                    @endif
                @endif
            </div>
        </div>

        <div class="kt-widget__info">
            @if($item)
                @if($item->IS_ADVANCE)
                    <span class="kt-badge kt-badge--brand kt-badge--inline kt-font-bold kt-font-transform-u mr-2">{{ KJLocalization::translate('Admin - Facturen', 'Voorschotfactuur', 'Voorschotfactuur') }}</span>

                    @if($item->project->INVOICING_COMPLETE == true)
                        <span class="kt-badge kt-badge--danger kt-badge--inline kt-font-bold kt-font-transform-u mr-2">{{ KJLocalization::translate('Admin - Facturen', 'Dossier slotfactuur al verstuurd', 'Dossier slotfactuur al verstuurd') }}!</span>
                    @endif
                @endif
            @endif
        </div>

        <div class="kt-widget__info mt-3">
            <div class="kt-widget__desc">
                {{ Form::open(array(
                    'method' => 'post',
                    'id' => 'invoice_default',
                    'class' => 'kt-form',
                    'novalidate'
                )) }}
                    {{ Form::hidden('ID', $item ? $item->ID : -1) }}
                    @if($default_label > 0)
                        {{ Form::hidden('FK_CORE_LABEL', $item ? $item->FK_CORE_LABEL : $default_label) }}
                    @endif

                    <div class="row">
                        <div class="col-xl-4 col-lg-6">
                            @if($default_label == null)
                                {{--Required--}} {{ KJField::select('FK_CORE_LABEL', KJLocalization::translate('Admin - Facturen', 'Administratie', 'Administratie'), $labels, $item ? $item->FK_CORE_LABEL : '', true, 0, ['required', 'data-screen-mode' => 'read, edit']) }}
                            @endif

                            @php
                                $buttonSelect = ['type' => 'button', 'caption' => KJLocalization::translate('Admin - Facturen', 'Relatie', 'Relatie'), 'class' => 'btn btn-primary btn-sm selectRelation'];
                                $buttonOpen = ['type' => 'button', 'caption' => KJLocalization::translate('Algemeen', 'Openen', 'Openen'), 'class' => 'btn btn-dark btn-sm openRelation'];

                                $relationButtonSet = [$buttonOpen];

                                if(($item ? $item->FK_CORE_WORKFLOWSTATE : config('workflowstate.INVOICE_CONCEPT')) == config('workflowstate.INVOICE_CONCEPT')) {
                                    $relationButtonSet = [$buttonSelect, $buttonOpen];
                                }
                            @endphp

                            {{--Required--}} {{ KJField::text('RELATION_NAME', KJLocalization::translate('Admin - Facturen', 'Relatie', 'Relatie'), $item ? ($item->relation ? $item->relation->title : '') : '-', true, ['readonly', 'required', 'data-screen-mode' => 'edit'], [
                                 'right' => $relationButtonSet
                            ]) }}
                            {{ Form::hidden('FK_CRM_RELATION', $item ? $item->FK_CRM_RELATION : null, ['required']) }}

                            @if(($item ? $item->FK_CORE_WORKFLOWSTATE : config('workflowstate.INVOICE_CONCEPT')) == config('workflowstate.INVOICE_CONCEPT'))
                                {{ KJField::select('FK_CRM_CONTACT', KJLocalization::translate('Admin - Facturen', 'Contactpersoon', 'Contactpersoon'), $contacts, $item ? $item->FK_CRM_CONTACT : '', true, 0, ['required', 'data-screen-mode' => 'read, edit']) }}
                                {{ KJField::select('FK_CRM_RELATION_ADDRESS', KJLocalization::translate('Admin - Facturen', 'Adres', 'Adres'), $addresses, $item ? $item->FK_CRM_RELATION_ADDRESS : '', true, 0, ['data-screen-mode' => 'edit']) }}
                            @else
                                {{ KJField::text('CONTACT_NAME', KJLocalization::translate('Admin - Facturen', 'Contactpersoon', 'Contactpersoon'), $item ? $item->contact->title : '', true, ['readonly', 'data-screen-mode' => 'read, edit']) }}
                            @endif

                            {{-- ADRES WEERGAVE --}}
                            <div class="md-form">
                                <p data-screen-mode="read">
                                    <label class="active" style="top: -12px;">{{ KJLocalization::translate('Admin - Facturen', 'Adres', 'Adres') }}</label>
                                    {!! ($item && $item->address) ? nl2br($item->address->fullAddress) : '-' !!}
                                </p>
                            </div>

                            @if(isset($item->NUMBER))
                                {{ KJField::text('DATE', KJLocalization::translate('Admin - Facturen', 'Datum', 'Datum'), $item ? $item->DateFormatted : '', true, ['readonly', 'data-screen-mode' => 'read, edit']) }}
                                {{ KJField::text('EXPIRATION_DATE', KJLocalization::translate('Admin - Facturen', 'Vervaldatum', 'Vervaldatum'), $item ? $item->ExpirationDateFormatted : '', true, ['readonly', 'data-screen-mode' => 'read, edit']) }}
                            @endif

                            {{ KJField::text('STATE', KJLocalization::translate('Admin - Facturen', 'Status', 'Status'), $item->workflowstate->DESCRIPTION ?? KJLocalization::translate('Admin - Facturen', 'Concept', 'Concept'), true, ['readonly', 'data-screen-mode' => 'read, edit']) }}

                            {{ KJField::textarea('REMARKS', KJLocalization::translate('Admin - Facturen', 'Opmerkingen intern', 'Opmerkingen intern'), $item ? $item->REMARKS : '', 3) }}
                            <div class="md-form">
                                <div class="form-group">
                                    <a href="javascript:;" id="insertTimestamp" data-user="{{ Auth::guard()->user()->title }}" class="btn btn-outline-brand btn-sm" data-screen-mode="edit">
                                        <i class="la la-clock-o"></i>
                                        {{ KJLocalization::translate('Admin - Facturen', 'Opmerking toevoegen', 'Opmerking toevoegen')}}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-6">
                            {{ KJField::text('TOTAL_EXCL', KJLocalization::translate('Admin - Facturen', 'Excl. btw', 'Excl. btw'), $item ? $item->TotalExclFormatted : '-', true, ['readonly', 'data-screen-mode' => 'read']) }}
                            {{ KJField::text('TOTAL_VAT', KJLocalization::translate('Admin - Facturen', 'Btw bedrag', 'Btw bedrag'), $item ? $item->TotalVatFormatted : '-', true, ['readonly', 'data-screen-mode' => 'read']) }}
                            {{ KJField::text('TOTAL_INCL', KJLocalization::translate('Admin - Facturen', 'Incl. btw', 'Incl. btw'), $item ? $item->TotalInclFormatted : '-', true, ['readonly', 'data-screen-mode' => 'read']) }}

                            @if(isset($item->NUMBER))
                                {{ KJField::text('DAYS_REMAINING', KJLocalization::translate('Admin - Facturen', 'Aantal dagen', 'Aantal dagen'), $item ? $item->getDaysRemaining() : '-', true, ['readonly', 'data-screen-mode' => 'read']) }}
                                {{ KJField::text('PAID', KJLocalization::translate('Admin - Facturen', 'Betaald', 'Betaald'), $item ? $item->PaidFormatted : '-', true, ['readonly', 'data-screen-mode' => 'read']) }}
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            @if(!$item)
                                {{ KJField::saveCancel(
                                    'btnSaveInvoiceNew',
                                    'btnCancelInvoiceNew',
                                    true,
                                    array(
                                        'saveText' => KJLocalization::translate('Algemeen', 'Toevoegen', 'Toevoegen'), 'saveStyle' => 'btn-success',
                                        'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren'), 'cancelStyle' => 'btn-secondary'
                                    )
                                ) }}
                            @else
                                {{ KJField::saveCancel(
                                    'btnSaveInvoice',
                                    'btnCancelInvoice',
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