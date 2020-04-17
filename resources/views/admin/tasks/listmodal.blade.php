{{ Form::open([
    'method' => 'post',
    'id' => 'detailFormTasks',
    'class' => 'kt-form',
    'novalidate'
]) }}
    <div class="kt-portlet__body">
        {{ Form::hidden('FK_PROJECT', ( $project ? $project->ID : -1 )) }}
        {{ Form::hidden('FK_CORE_USER_CREATED', ( $item ? $item->FK_CORE_USER_CREATED : Auth::guard()->user()->ID )) }}

        <div class="row">
            <div class="col-xl-12 col-lg-6">
                {{ KJField::select('FK_CORE_USER_ASSIGNEE', KJLocalization::translate('Admin - Taken', 'Toegewezen aan', 'Toegewezen aan'), $contacts, ($item ? $item->FK_CORE_USER_ASSIGNEE : ''), true, 0, ['required']) }}
                {{ KJField::select('FK_TASK_LIST', KJLocalization::translate('Admin - Taken', 'Standaard takenlijst', 'Standaard takenlijst'), $taskLists, ($item ? $item->FK_TASK_LIST : ''), true, 0, ['required']) }}
                {{ KJField::date('STARTDATE', KJLocalization::translate('Admin - Taken', 'Start datum', 'Start datum'), (date(KJ\Localization\libraries\LanguageUtils::getDateFormat(), strtotime(date("D M d")))), ['required', 'data-date-start-date' => '-0d', 'data-locale-format' => \KJ\Localization\libraries\LanguageUtils::getJSDatePickerFormat()]) }}
            </div>
        </div>

        <div class="row mt-4">
            <div class="col">
                <h5 class="mb-3">{{ KJLocalization::translate('Algemeen', 'Aanvullende gegevens', 'Aanvullende gegevens') }}</h5>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12 col-lg-6">

                {{ KJField::text('TITLE', KJLocalization::translate('Admin - Taken', 'Dossier gekoppeld', 'Dossier gekoppeld'), ( $project ?  ($project->DESCRIPTION? $project->DESCRIPTION : $project->getTitleAttribute())  : '' ), true, ['readonly', 'data-screen-mode' => 'read, edit']) }}
                {{ KJField::text('USER_CREATED', KJLocalization::translate('Admin - Taken', 'Aangemaakt door', 'Aangemaakt door'), ( $item ? ($item->user_created ? $item->user_created->title : '') : Auth::guard()->user()->title ), true, ['readonly', 'data-screen-mode' => 'read, edit']) }}
                {{ KJField::text('DATE_CREATED', KJLocalization::translate('Admin - Taken', 'Aangemaakt op', 'Aangemaakt op'), ( $item ? $item->getCreatedDateFormattedAttribute() : date(\KJ\Localization\libraries\LanguageUtils::getDateTimeFormat()) ), true, ['readonly', 'data-screen-mode' => 'read, edit']) }}
            </div>
        </div>
    </div>
{{ Form::close() }}