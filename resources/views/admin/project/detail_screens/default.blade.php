<div class="kt-widget__top">
    <div class="kt-widget__content">
        <div class="kt-widget__head">
            <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/project') }}" class="back-button"></a>
            <h4>
                {{ ($item ? ($item->DESCRIPTION? $item->DESCRIPTION : $item->getTitleAttribute()) : KJLocalization::translate('Admin - Dossiers', 'Nieuw dossier', 'Nieuw dossier')) }}
                <button type="button" class="btn btn-sm btn-icon setEditMode" data-target="default"><i class="fa fa-pen"></i></button>
            </h4>

            <div class="kt-widget__action">
                {{-- STATUS --}}
                <div class="btn-group btn-group" role="group">
                    @if($previousWorkflowstate)
                        <button type="button" class="btn btn-group-item btn-secondary btn-sm btn-upper processWorkflowstate kt-margin-b-5-tablet-and-mobile" data-id="{{ $item->ID }}" data-state="{{ $previousWorkflowstate->ID }}">{{ KJLocalization::translate('Admin - Dossiers', 'Terug naar', 'Terug naar') }}: {{ $previousWorkflowstate->DESCRIPTION }}</button>
                    @endif

                    @if($nextWorkflowstate)
                        <button type="button" class="btn btn-group-item btn-secondary btn-sm btn-upper processWorkflowstate kt-margin-b-5-tablet-and-mobile" data-id="{{ $item->ID }}" data-state="{{ $nextWorkflowstate->ID }}">{{ $item->workflowstate->ACTION_DESCRIPTION }}</button>
                    @endif
                </div>

                @if($item)
                    @if($item->ACTIVE ?? true)
                        <button type="button" class="btn btn-danger btn-sm btn-upper activateItem" data-id="{{ $item->ID }}">{{ KJLocalization::translate('Algemeen', 'Archiveren', 'Archiveren') }}</button>
                    @else
                        <button type="button" class="btn btn-success btn-sm btn-upper activateItem" data-id="{{ $item->ID }}">{{ KJLocalization::translate('Algemeen', 'Activeren', 'Activeren') }}</button>
                    @endif
                @endif
            </div>
        </div>

        @if($item)
            <div class="kt-widget__info">
                <div class="kt-widget__progress">
                    <div class="progress" style="height: 16px; width: 100%;">
                        <div class="progress-bar kt-bg-brand" role="progressbar" style="width: {{ $item->progress() }}%;">
                            {{ $item->workflowstate ? $item->workflowstate->DESCRIPTION : '' }}
                        </div>
                    </div>
                    <div class="kt-widget__stats">
                        {{ $item->progress() }}%
                    </div>
                </div>
            </div>
        @endif

        <div class="kt-widget__info mt-3">
            <div class="kt-widget__desc">
                {{ Form::open(array(
                    'method' => 'post',
                    'id' => 'project_default',
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
                                {{--Required--}} {{ KJField::select('FK_CORE_LABEL', KJLocalization::translate('Admin - Dossiers', 'Administratie', 'Administratie'), $labels, $item ? $item->FK_CORE_LABEL : '', true, 0, ['required', 'data-screen-mode' => 'read, edit']) }}
                            @endif

                            {{--Required--}} {{ KJField::select('FK_CORE_DROPDOWNVALUE_PROJECTTYPE', KJLocalization::translate('Admin - Dossiers', 'Soort traject', 'Soort traject'), $project_types, $item ? $item->FK_CORE_DROPDOWNVALUE_PROJECTTYPE : '', true, config('dropdown_type.TYPE_PROJECTTYPE'), ['required', 'data-screen-mode' => 'read, edit']) }}
                            @php
                                $relationButtons = [];
                                $relationButtons[] = ['type' => 'button', 'caption' => KJLocalization::translate('Admin - Dossiers', 'Relatie', 'Relatie'), 'class' => 'btn btn-primary btn-sm selectRelation'];
                                if (Auth::guard()->user()->hasPermission(config('permission.CRM'))) {
                                    $relationButtons[] = ['type' => 'button', 'caption' => KJLocalization::translate('Algemeen', 'Openen', 'Openen'), 'class' => 'btn btn-dark btn-sm openRelation'];
                                }
                            @endphp
                            {{--Required--}} {{ KJField::text('REFERRER_NAME', KJLocalization::translate('Admin - Dossiers', 'Verwijzer', 'Verwijzer'), $item ? ($item->referrer ? $item->referrer->title : '') : '-', true, ['readonly', 'required', 'data-screen-mode' => 'read, edit', 'data-update' => 'FK_CRM_CONTACT_REFERRER'], [
                                 'right' => $relationButtons
                            ]) }}
                            {{ Form::hidden('FK_CRM_RELATION_REFERRER', $item ? $item->FK_CRM_RELATION_REFERRER : null, ['required']) }}
                            {{--Required--}} {{ KJField::select('FK_CRM_CONTACT_REFERRER', KJLocalization::translate('Admin - Dossiers', 'Contactpersoon verwijzer', 'Contactpersoon verwijzer'), $contacts_referrer, $item ? $item->FK_CRM_CONTACT_REFERRER : '', true, 0, ['required', 'data-screen-mode' => 'read, edit']) }}

                            {{--Required--}} {{ KJField::text('EMPLOYER_NAME', KJLocalization::translate('Admin - Dossiers', 'Werkgever', 'Werkgever'), $item ? ($item->employer ? $item->employer->title : '') : '-', true, ['readonly', 'required', 'data-screen-mode' => 'read, edit', 'data-update' => 'FK_CRM_CONTACT_EMPLOYER'], [
                                 'right' => $relationButtons
                            ]) }}
                            {{ Form::hidden('FK_CRM_RELATION_EMPLOYER', $item ? $item->FK_CRM_RELATION_EMPLOYER : null, ['required']) }}
                            {{ KJField::select('FK_CRM_CONTACT_EMPLOYER', KJLocalization::translate('Admin - Dossiers', 'Contactpersoon werkgever', 'Contactpersoon werkgever'), $contacts_employer, $item ? $item->FK_CRM_CONTACT_EMPLOYER : '', true, 0, ['data-screen-mode' => 'read, edit']) }}

                            @php
                                $contactButtons = [];
                                $contactButtons[] = ['type' => 'button', 'caption' => KJLocalization::translate('Admin - Dossiers', 'Contactpersoon', 'Contactpersoon'), 'class' => 'btn btn-primary btn-sm selectContact'];
                                if (Auth::guard()->user()->hasPermission(config('permission.CRM'))) {
                                    $contactButtons[] = ['type' => 'button', 'caption' => KJLocalization::translate('Algemeen', 'Openen', 'Openen'), 'class' => 'btn btn-dark btn-sm openContact'];
                                }
                            @endphp
                            {{--Required--}} {{ KJField::text('EMPLOYEE_NAME', KJLocalization::translate('Admin - Dossiers', 'Werknemer', 'Werknemer'), $item ? ($item->employee ? $item->employee->title : '') : '-', true, ['readonly', 'required', 'data-screen-mode' => 'read, edit'], [
                                 'right' => $contactButtons
                            ]) }}
                            {{ Form::hidden('FK_CRM_CONTACT_EMPLOYEE', $item ? $item->FK_CRM_CONTACT_EMPLOYEE : null, ['required']) }}

                            {{ KJField::text('DESCRIPTION', KJLocalization::translate('Admin - Dossiers', 'Dossiernaam', 'Dossiernaam'), $item ? $item->DESCRIPTION : '-', true, ['required', 'data-screen-mode' => 'edit'] ) }}
                        </div>

                        <div class="col-xl-4 col-lg-6">

                            @php
                                $compensation_readonly = '';
                                $compensation_can_change = true;

                                if (($item && ($item->INVOICING_COMPLETE ?? false)) == true) {
                                    $compensation_readonly = 'readonly';
                                    $compensation_can_change = false;
                                }
                                else if ($item && ($item->hasInvoices())) {
                                    $compensation_readonly = 'readonly';
                                    $compensation_can_change = false;
                                }

                            @endphp
                            @if($compensation_can_change)
                                {{ KJField::checkbox('COMPENSATED', KJLocalization::translate('Admin - Dossiers', 'Wordt vergoed', 'Wordt vergoed'), true, ( $item ? $item->COMPENSATED : false ), true, ['disabled', 'data-screen-mode' => 'read,edit']) }}
                            @else
                                {{ KJField::text('COMPENSATED_READ', KJLocalization::translate('Admin - Dossiers', 'Wordt vergoed', 'Wordt vergoed'), $item ? ($item->COMPENSATED ? KJLocalization::translate('Admin - Dossiers', 'Ja', 'Ja') : KJLocalization::translate('Admin - Dossiers', 'Nee', 'Nee')) : '' , true, [$compensation_readonly, 'data-screen-mode' => 'read, edit']) }}
                            @endif
                            {{ KJField::number('COMPENSATION_PERCENTAGE', KJLocalization::translate('Admin - Dossiers', 'Vergoedingspercentage', 'Vergoedingspercentage'), $item ? $item->getCompensationPercentageDecimalAttribute() : '', true, [$item ? ($item->COMPENSATED ? 'required' : '') : '',$compensation_readonly, 'data-screen-mode' => 'read, edit', 'min' => 0], [
                                'right' => [['type' => 'text', 'caption' => '%']]]
                            ) }}

                            {{--Required--}} {{ KJField::date('START_DATE', KJLocalization::translate('Admin - Dossiers', 'Eerste ziektedag', 'Eerste ziektedag'), $item ? $item->getStartDateFormattedAttribute() : '', ['required', 'data-screen-mode' => 'read, edit', 'data-date-end-date' => '-0d', 'data-locale-format' => \KJ\Localization\libraries\LanguageUtils::getJSDatePickerFormat()]) }}
                            {{--Required--}} {{ KJField::text('POLICY_NUMBER', KJLocalization::translate('Admin - Dossiers', 'Polisnummer', 'Polisnummer'), $item ? $item->POLICY_NUMBER : '', true, ['required', 'data-screen-mode' => 'read, edit']) }}
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
                                    <div class="col-xl-4 col-lg-6">
                                        {{ KJField::text('USER_CREATED', KJLocalization::translate('Admin - Dossiers', 'Aangemaakt door', 'Aangemaakt door'), $item ? ($item->user_created ? $item->user_created->title : '') : Auth::guard()->user()->title, true, ['readonly', 'data-screen-mode' => 'read, edit']) }}
                                        {{ KJField::text('DATE_CREATED', KJLocalization::translate('Admin - Dossiers', 'Aangemaakt op', 'Aangemaakt op'), $item ? $item->getCreatedDateFormattedAttribute() : date(\KJ\Localization\libraries\LanguageUtils::getDateTimeFormat()), true, ['readonly', 'data-screen-mode' => 'read, edit']) }}
                                    </div>
                                </div>
                            @endcomponent
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            @if(!$item)
                                {{ KJField::saveCancel(
                                    'btnSaveProjectNew',
                                    'btnCancelProjectNew',
                                    true,
                                    array(
                                        'saveText' => KJLocalization::translate('Algemeen', 'Toevoegen', 'Toevoegen'), 'saveStyle' => 'btn-success',
                                        'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren'), 'cancelStyle' => 'btn-secondary'
                                    )
                                ) }}
                            @else
                                {{ KJField::saveCancel(
                                    'btnSaveProject',
                                    'btnCancelProject',
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