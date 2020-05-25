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
                @case(config('task_type.TYPE_PRODUCT'))
                {{ Form::hidden('FK_ASSORTMENT_PRODUCT', request('pid')) }}
                @break
            @endswitch
        @endif

        <div class="row">
            <div class="col-xl-12 col-lg-6">
                {{ KJField::text('SUBJECT', KJLocalization::translate('Admin - Taken', 'Onderwerp', 'Onderwerp'), ( $item ? $item->SUBJECT : '' ), true, ['required']) }}
                {{ KJField::textarea('CONTENT', KJLocalization::translate('Admin - Taken', 'Inhoud taak', 'Inhoud taak'), ( $item ? $item->CONTENT : '' ), 3) }}
                @if((request('type') != config('task_type.TYPE_TASKLIST') && (request('type') != config('task_type.TYPE_PRODUCT'))))
                    {{ KJField::date('DEADLINE', KJLocalization::translate('Admin - Taken', 'Deadline', 'Deadline'), ($item ? $item->getDeadlineDatePickerFormattedAttribute() : ''), ['required', 'data-date-start-date' => '-0d', 'data-locale-format' => \KJ\Localization\libraries\LanguageUtils::getJSDatePickerFormat()]) }}
                    {{ KJField::date('REMINDER_DATE', KJLocalization::translate('Admin - Taken', 'Herinneringsdatum', 'Herinneringsdatum'), ($item ? $item->getReminderDateDatePickerFormattedAttribute() : ''), ['required', 'data-date-start-date' => '-0d', 'data-locale-format' => \KJ\Localization\libraries\LanguageUtils::getJSDatePickerFormat()]) }}
                @endif
                @if(request('pid') && (request('type') == config('task_type.TYPE_RELATION')))
                    {{ KJField::select('FK_PROJECT', KJLocalization::translate('Admin - Taken', 'Gekoppeld dossier', 'Gekoppeld dossier'), $projects, ( $item ? $item->FK_PROJECT : '' ), true, 0, ['data-live-search' => 1, 'data-size' => 5]) }}
                @endif
            </div>
        </div>

        @if(request('pid') && ((request('type') == config('task_type.TYPE_TASKLIST')) || (request('type') == config('task_type.TYPE_PRODUCT'))))
            {{-- Required --}} {{ KJField::number('REMEMBER_DATES',KJLocalization::translate('Admin - Taken', 'Herrinneringsdagen', 'Herrinneringsdagen'), $item ? $item->REMEMBER_DATES : '', true, ['required', 'min' => 0, 'steps' => 1]) }}
            {{-- Required --}} {{ KJField::number('EXPIRATION_DATES',KJLocalization::translate('Admin - Taken', 'Vervaldagen', 'Vervaldagen'), $item ? $item->EXPIRATION_DATES : '', true, ['required', 'min' => 0, 'steps' => 1]) }}
        @endif

        @if(request('pid') && (request('type') == config('task_type.TYPE_PROJECT')))
            {{ KJField::select('FK_PROJECT_ASSORTMENT_PRODUCT', KJLocalization::translate('Admin - Taken', 'Interventie', 'Interventie'), $products, $item ? $item->FK_PROJECT_ASSORTMENT_PRODUCT : '', true, 0, ['data-screen-mode' => 'read, edit']) }}
        @endif

        <div class="row">
            <div class="col-xl-12 col-lg-10">
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
                </div>
            </div>
        </div>

        @if(!(request('pid') && ((request('type') == config('task_type.TYPE_TASKLIST')) || (request('type') == config('task_type.TYPE_PRODUCT')))))
            <div class="row mt-4">
                <div class="col">
                    <h5 class="mb-3">{{ KJLocalization::translate('Admin - Taken', 'Opvolging taak', 'Opvolging taak') }}</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12 col-lg-6">
                    {{ KJField::select('FK_CORE_USER_ASSIGNEE', KJLocalization::translate('Admin - Taken', 'Toegewezen aan', 'Toegewezen aan'), $users, ($item ? $item->FK_CORE_USER_ASSIGNEE : ''), true, 0, ['required']) }}
                </div>
            </div>
        @endif
    </div>
{{ Form::close() }}