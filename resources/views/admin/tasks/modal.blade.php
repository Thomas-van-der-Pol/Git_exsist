{{ Form::open([
    'method' => 'post',
    'id' => 'detailFormTasks',
    'class' => 'kt-form',
    'novalidate'
]) }}
    <div class="kt-portlet__body">
        {{ Form::hidden('ID', ( $item ? $item->ID : -1 )) }}
        {{ Form::hidden('FK_CORE_USER_CREATED', ( $item ? $item->FK_CORE_USER_CREATED : Auth::guard()->user()->ID )) }}

        @if(request('pid'))
            @switch(request('type'))
                @case(config('task_type.TYPE_RELATION'))
                {{ Form::hidden('FK_CRM_RELATION', request('pid')) }}
                @break

                @case(config('task_type.TYPE_PROJECT'))
                {{ Form::hidden('FK_PROJECT', request('pid')) }}
                @break

                @case(config('task_type.TYPE_TASKLIST'))
                {{ Form::hidden('FK_TASK_LIST', request('pid')) }}
                @break
            @endswitch
        @endif

        <div class="row">
            <div class="col-xl-12 col-lg-6">
                {{ KJField::text('SUBJECT', KJLocalization::translate('Admin - Taken', 'Onderwerp', 'Onderwerp'), ( $item ? $item->SUBJECT : '' ), true, ['required']) }}
                {{ KJField::textarea('CONTENT', KJLocalization::translate('Admin - Taken', 'Inhoud taak', 'Inhoud taak'), ( $item ? $item->CONTENT : '' ), 3) }}
                @if(request('pid') && (request('type') != config('task_type.TYPE_TASKLIST')))
                    {{ KJField::date('DEADLINE', KJLocalization::translate('Admin - Taken', 'Deadline', 'Deadline'), ($item ? $item->getDeadlineDatePickerFormattedAttribute() : ''), ['required', 'data-date-start-date' => '-0d', 'data-locale-format' => \KJ\Localization\libraries\LanguageUtils::getJSDatePickerFormat()]) }}
                @endif
                @if(request('pid') && (request('type') == config('task_type.TYPE_RELATION')))
                    {{ KJField::select('FK_PROJECT', KJLocalization::translate('Admin - Taken', 'Gekoppeld dossier', 'Gekoppeld dossier'), $projects, ( $item ? $item->FK_PROJECT : '' ), true, 0, ['data-live-search' => 1, 'data-size' => 5]) }}
                @endif
            </div>
        </div>

        @if(request('pid') && (request('type') == config('task_type.TYPE_TASKLIST')))
            {{-- Required --}} {{ KJField::number('REMEMBER_DATES',KJLocalization::translate('Admin - Taken', 'Herrinneringsdagen', 'Herrinneringsdagen'), $item ? $item->REMEMBER_DATES : 0, true, ['required', 'min' => 0, 'steps' => 1]) }}
            {{-- Required --}} {{ KJField::number('EXPIRATION_DATES',KJLocalization::translate('Admin - Taken', 'Vervaldagen', 'Vervaldagen'), $item ? $item->EXPIRATION_DATES : 0, true, ['required', 'min' => 0, 'steps' => 1]) }}
        @else
            <div class="row mt-4">
                <div class="col">
                    <h5 class="mb-3">{{ KJLocalization::translate('Admin - Taken', 'Opvolging taak', 'Opvolging taak') }}</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12 col-lg-6">
                    {{ KJField::select('FK_CORE_USER_ASSIGNEE', KJLocalization::translate('Admin - Taken', 'Toegewezen aan', 'Toegewezen aan'), $users, ($item ? $item->FK_CORE_USER_ASSIGNEE : ''), true, 0, ['required']) }}
                    {{ KJField::checkbox('DONE', KJLocalization::translate('Admin - Taken', 'Gereed', 'Gereed'), true, ( $item ? $item->DONE : false ), true) }}
                </div>
            </div>
        @endif

        <div class="row mt-4">
            <div class="col">
                <h5 class="mb-3">{{ KJLocalization::translate('Algemeen', 'Aanvullende gegevens', 'Aanvullende gegevens') }}</h5>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12 col-lg-6">
                {{ KJField::text('USER_CREATED', KJLocalization::translate('Admin - Taken', 'Aangemaakt door', 'Aangemaakt door'), ( $item ? ($item->user_created ? $item->user_created->title : '') : Auth::guard()->user()->title ), true, ['readonly', 'data-screen-mode' => 'read, edit']) }}
                {{ KJField::text('DATE_CREATED', KJLocalization::translate('Admin - Taken', 'Aangemaakt op', 'Aangemaakt op'), ( $item ? $item->getCreatedDateFormattedAttribute() : date(\KJ\Localization\libraries\LanguageUtils::getDateTimeFormat()) ), true, ['readonly', 'data-screen-mode' => 'read, edit']) }}
            </div>
        </div>
    </div>
{{ Form::close() }}