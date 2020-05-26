<div class="kt-widget__top">
    <div class="kt-widget__content">
        <div class="kt-widget__head">
            @php
            $backURL = null;

            if($type == config('task_type.TYPE_PROJECT')) {
                $backURL = 'admin/project/detail/'.$item->project->ID;
            }
            else if($type == config('task_type.TYPE_RELATION')) {
                $backURL = 'admin/crm/relation/detail/'.$item->relation->ID;
            }
            else if($type == config('task_type.TYPE_TASKLIST')) {
                $backURL = 'admin/settings/tasklist/detail/'.$item->taskList->ID;
            }
            else if($type == config('task_type.TYPE_PRODUCT')) {
                $backURL = 'admin/product/detail/'.$item->product->ID;
            }
            else {
                $backURL = 'admin/tasks';
            }
            @endphp
            <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl( $backURL) }}" class="back-button"></a>
            <h4>{{ ( $item ? ( $item->SUBJECT ?? KJLocalization::translate('Admin - Taken', 'Nieuwe taak', 'Nieuwe taak') ) : KJLocalization::translate('Admin - Taken', 'Nieuwe taak', 'Nieuwe taak') ) }} <button type="button" class="btn btn-sm btn-icon setEditMode" data-target="default"><i class="fa fa-pen"></i></button></h4>

            <div class="kt-widget__action">
                @if($item)
                    @if($item->ACTIVE ?? true)
                        <button type="button" class="btn btn-danger btn-sm btn-upper activateItem" data-id="{{ ( $item ? $item->ID : 0 ) }}">{{ KJLocalization::translate('Algemeen', 'Archiveren', 'Archiveren') }}</button>
                    @else
                        <button type="button" class="btn btn-success btn-sm btn-upper activateItem" data-id="{{ ( $item ? $item->ID : 0 )}}">{{ KJLocalization::translate('Algemeen', 'Activeren', 'Activeren') }}</button>
                    @endif
                @endif
            </div>
        </div>

        <div class="kt-widget__info mt-3">
            <div class="kt-widget__desc">
                {{ Form::open([
                    'method' => 'post',
                    'id' => 'detailFormTasks',
                    'class' => 'kt-form',
                    'novalidate'
                ]) }}
                {{ Form::hidden('ID', ( $item ? $item->ID : -1 )) }}
                {{ Form::hidden('FK_CORE_USER_CREATED', ( $item ? $item->FK_CORE_USER_CREATED : Auth::guard()->user()->ID )) }}

                    <div class="row">
                        <div class="col-xl-4 col-lg-6">
                            {{ KJField::text('SUBJECT', KJLocalization::translate('Admin - Taken', 'Onderwerp', 'Onderwerp'), ( $item ? $item->SUBJECT : '' ), true, ['required']) }}
                            {{ KJField::textarea('CONTENT', KJLocalization::translate('Admin - Taken', 'Inhoud taak', 'Inhoud taak'), ( $item ? $item->CONTENT : '' ), 3) }}

                            @if($item->FK_TASK_LIST || ($item->FK_ASSORTMENT_PRODUCT && !$item->FK_PROJECT))
                                {{-- Required --}} {{ KJField::number('REMEMBER_DATES',KJLocalization::translate('Admin - Taken', 'Herinneringsdagen', 'Herinneringsdagen'), $item ? $item->REMEMBER_DATES : '', true, ['required', 'min' => 0, 'step' => 1]) }}
                                {{-- Required --}} {{ KJField::number('EXPIRATION_DATES',KJLocalization::translate('Admin - Taken', 'Vervaldagen', 'Vervaldagen'), $item ? $item->EXPIRATION_DATES : '', true, ['required', 'min' => 0, 'step' => 1]) }}
                            @else
                                {{ KJField::date('DEADLINE', KJLocalization::translate('Admin - Taken', 'Deadline', 'Deadline'), ($item ? $item->getDeadlineDatePickerFormattedAttribute() : ''), ['required', 'data-date-start-date' => '-0d', 'data-locale-format' => \KJ\Localization\libraries\LanguageUtils::getJSDatePickerFormat()]) }}
                                {{ KJField::date('REMINDER_DATE', KJLocalization::translate('Admin - Taken', 'Herinneringsdatum', 'Herinneringsdatum'), ($item ? $item->getReminderDateDatePickerFormattedAttribute() : ''), ['required', 'data-date-start-date' => '-0d', 'data-locale-format' => \KJ\Localization\libraries\LanguageUtils::getJSDatePickerFormat()]) }}
                            @endif
                        </div>

                        <div class="col-xl-4 col-lg-6">
                            @if($item->canFollowUp())
                                <h5 class="mb-3">{{ KJLocalization::translate('Admin - Taken', 'Opvolging taak', 'Opvolging taak') }}</h5>
                                {{ KJField::select('FK_CORE_USER_ASSIGNEE', KJLocalization::translate('Admin - Taken', 'Toegewezen aan', 'Toegewezen aan'), $users, ( $item ? ($item->FK_CORE_USER_ASSIGNEE ?? Auth::guard()->user()->ID) : Auth::guard()->user()->ID ), true) }}

                                <h5 class="mb-3">{{ KJLocalization::translate('Admin - Taken', 'Voortgang taak', 'Voortgang taak') }}</h5>
                                {{ KJField::select('PROGRESS', KJLocalization::translate('Admin - Taken', 'Voortgang', 'Voortgang'), $progressOptions, ( $item ? ($item->progress() ? $item->progress(): '') : ''), true) }}
                                @if(($item->DONE ?? false) == true)
                                    {{ KJField::text('USER_DONE', KJLocalization::translate('Admin - Taken', 'Gereed gezet door', 'Gereed gezet door'), ( $item ? ($item->user_done ? $item->user_done->title : '') : ''), true, ['readonly', 'data-screen-mode' => 'read, edit']) }}
                                    {{ KJField::text('DONE_DATE', KJLocalization::translate('Admin - Taken', 'Gereed gezet op', 'Gereed gezet op'), ( $item ? $item->getDoneDateFormattedAttribute() : ''), true, ['readonly', 'data-screen-mode' => 'read, edit']) }}
                                @endif
                                @if(($item->STARTED ?? false) == true)
                                    {{ KJField::text('USER_STARTED', KJLocalization::translate('Admin - Taken', 'Gestart door', 'Gestart door'), ( $item ? ($item->user_started ? $item->user_started->title : '') : ''), true, ['readonly', 'data-screen-mode' => 'read, edit']) }}
                                    {{ KJField::text('STARTED_DATE', KJLocalization::translate('Admin - Taken', 'Gestart op', 'Gestart op'), ( $item ? $item->getStartedDateFormattedAttribute() : ''), true, ['readonly', 'data-screen-mode' => 'read, edit']) }}
                                @endif
                            @endif
                        </div>
                    </div>


                    @if($item->FK_CRM_RELATION || $item->FK_PROJECT )
                        <div class="row mt-4">
                            <div class="col">
                                <h5 class="mb-3">{{ KJLocalization::translate('Admin - Taken', 'Koppelingen', 'Koppelingen') }}</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-4 col-lg-6">
                                @if($item->FK_CRM_RELATION)
                                    {{ KJField::text('RELATION_NAME', KJLocalization::translate('Admin - Taken', 'Relatie', 'Relatie'), $item ? ($item->relation ? $item->relation->title : '') : '-', true, ['readonly'], [
                                         'right' => [
                                              ['type' => 'button', 'caption' => KJLocalization::translate('Algemeen', 'Openen', 'Openen'), 'class' => 'btn btn-dark btn-sm openRelation']
                                         ]
                                    ]) }}
                                    {{ Form::hidden('FK_CRM_RELATION', $item ? $item->FK_CRM_RELATION : null) }}
                                @endif

                                @if($item->FK_PROJECT)
                                    {{ KJField::text('RELATION_PROJECT', KJLocalization::translate('Admin - Taken', 'Dossier', 'Dossier'), $item ? ($item->project ? $item->project->title : '') : '-', true, ['readonly'], [
                                         'right' => [
                                              ['type' => 'button', 'caption' => KJLocalization::translate('Algemeen', 'Openen', 'Openen'), 'class' => 'btn btn-dark btn-sm openProject']
                                         ]
                                    ]) }}
                                    {{ Form::hidden('FK_PROJECT', $item ? $item->FK_PROJECT : null) }}

                                    {{ KJField::select('FK_PROJECT_ASSORTMENT_PRODUCT', KJLocalization::translate('Admin - Taken', 'Interventie', 'Interventie'), $products, $item ? $item->FK_PROJECT_ASSORTMENT_PRODUCT : '', true, 0, ['data-screen-mode' => 'read, edit']) }}
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-xl-8 col-lg-10">
                            <div class="row">
                                <div class="col pr-0">
                                    <div class="md-form">
                                        <div class="form-group">
                                            {{ Form::text(
                                                'CATEGORIES',
                                                $item ? $item->categoriesAsText() : '',
                                                [
                                                    'class' => 'form-control exclude-screen-mode',
                                                    'placeholder' => KJLocalization::translate('Admin - Taken', 'Categorieën', 'Categorieën' ),
                                                    'id' => 'CATEGORIES',
                                                    'data-wl' => json_encode($categories)
                                                ]
                                            ) }}
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-end">
                                    <button class="btn btn-label-brand btn-square btn-bold btn-upper btn-sm btn-icon mb-3 kj_managebutton" data-id="{{ config('dropdown_type.TYPE_TASK_CATEGORY') }}">. . .</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($item->canFollowUp())
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
                                            {{ KJField::text('USER_CREATED', KJLocalization::translate('Admin - Taken', 'Aangemaakt door', 'Aangemaakt door'), ( $item ? ($item->user_created ? $item->user_created->title : '') : Auth::guard()->user()->title ), true, ['readonly', 'data-screen-mode' => 'read, edit']) }}
                                            {{ KJField::text('DATE_CREATED', KJLocalization::translate('Admin - Taken', 'Aangemaakt op', 'Aangemaakt op'), ( $item ? $item->getCreatedDateFormattedAttribute() : date(\KJ\Localization\libraries\LanguageUtils::getDateTimeFormat()) ), true, ['readonly', 'data-screen-mode' => 'read, edit']) }}
                                        </div>
                                    </div>
                                @endcomponent
                            </div>
                        </div>
                    @endif

                    {{ KJField::saveCancel(
                        'btnSaveTask',
                        'btnCancelTask',
                        true,
                        [
                            'saveText' => KJLocalization::translate('Algemeen', 'Opslaan', 'Opslaan'),
                            'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren')
                        ]
                    ) }}
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>