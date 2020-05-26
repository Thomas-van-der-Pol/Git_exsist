<div class="kt-portlet kt-portlet--mobile kt-todo" id="detailScreenContainer">
    <div class="kt-portlet__body kt-portlet__body--fit-x kt-todo__list" id="kt-todo__list">
        <div class="kt-todo__head">
            <div class="kt-todo__toolbar">
                <div class="kt-todo__actions kt-todo__actions--expanded">
                    @if($type != config('task_type.TYPE_TASKLIST') && $type != config('task_type.TYPE_PRODUCT'))
                        <div class="kt-todo__check">
                            <label class="kt-checkbox kt-checkbox--single kt-checkbox--tick kt-checkbox--brand">
                                <input type="checkbox" class="exclude-screen-mode">
                                <span></span>
                            </label>
                        </div>
                    @endif
                    <div class="kt-todo__panel">
                        @if($type != config('task_type.TYPE_TASKLIST') && $type != config('task_type.TYPE_PRODUCT'))
                            @if($type != config('task_type.TYPE_DONE'))
                            <div class="dropdown">
                                <button class=" kt-todo__icon ml-3" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24"/>
                                            <path d="M10.5,5 L19.5,5 C20.3284271,5 21,5.67157288 21,6.5 C21,7.32842712 20.3284271,8 19.5,8 L10.5,8 C9.67157288,8 9,7.32842712 9,6.5 C9,5.67157288 9.67157288,5 10.5,5 Z M10.5,10 L19.5,10 C20.3284271,10 21,10.6715729 21,11.5 C21,12.3284271 20.3284271,13 19.5,13 L10.5,13 C9.67157288,13 9,12.3284271 9,11.5 C9,10.6715729 9.67157288,10 10.5,10 Z M10.5,15 L19.5,15 C20.3284271,15 21,15.6715729 21,16.5 C21,17.3284271 20.3284271,18 19.5,18 L10.5,18 C9.67157288,18 9,17.3284271 9,16.5 C9,15.6715729 9.67157288,15 10.5,15 Z" fill="#000000"/>
                                            <path d="M5.5,8 C4.67157288,8 4,7.32842712 4,6.5 C4,5.67157288 4.67157288,5 5.5,5 C6.32842712,5 7,5.67157288 7,6.5 C7,7.32842712 6.32842712,8 5.5,8 Z M5.5,13 C4.67157288,13 4,12.3284271 4,11.5 C4,10.6715729 4.67157288,10 5.5,10 C6.32842712,10 7,10.6715729 7,11.5 C7,12.3284271 6.32842712,13 5.5,13 Z M5.5,18 C4.67157288,18 4,17.3284271 4,16.5 C4,15.6715729 4.67157288,15 5.5,15 C6.32842712,15 7,15.6715729 7,16.5 C7,17.3284271 6.32842712,18 5.5,18 Z" fill="#000000" opacity="0.3"/>
                                        </g>
                                    </svg>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item shiftDeadline" data-type = 'shiftDeadline' href="#">{{KJLocalization::translate('Admin - Taken', 'Deadline verschuiven', 'Deadline verschuiven')}}</a>
                                    <a class="dropdown-item connectEmployee" data-type = 'connectEmployee' href="#">{{KJLocalization::translate('Admin - Taken', 'Koppelen aan medewerker', 'Koppelen aan medewerker')}}</a>
                                    <a class="dropdown-item setDone" data-status = 'setdone' href="#">{{ KJLocalization::translate('Admin - Taken', 'Markeren als gereed', 'Markeren als gereed') }}</a>
                                    <a class="dropdown-item copyToMap" data-type = 'copyToMap' href="#">{{ KJLocalization::translate('Admin - Taken', 'Toevoegen aan map', 'Toevoegen aan map') }}</a>
                                </div>
                            </div>
                            @else
                                <button class="kt-todo__icon ml-3 setDone" data-status = 'reopen' data-toggle="kt-tooltip" title="" data-original-title="{{ KJLocalization::translate('Admin - Taken', 'Heropenen', 'Heropenen') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24"/>
                                            <path d="M8.43296491,7.17429118 L9.40782327,7.85689436 C9.49616631,7.91875282 9.56214077,8.00751728 9.5959027,8.10994332 C9.68235021,8.37220548 9.53982427,8.65489052 9.27756211,8.74133803 L5.89079566,9.85769242 C5.84469033,9.87288977 5.79661753,9.8812917 5.74809064,9.88263369 C5.4720538,9.8902674 5.24209339,9.67268366 5.23445968,9.39664682 L5.13610134,5.83998177 C5.13313425,5.73269078 5.16477113,5.62729274 5.22633424,5.53937151 C5.384723,5.31316892 5.69649589,5.25819495 5.92269848,5.4165837 L6.72910242,5.98123382 C8.16546398,4.72182424 10.0239806,4 12,4 C16.418278,4 20,7.581722 20,12 C20,16.418278 16.418278,20 12,20 C7.581722,20 4,16.418278 4,12 L6,12 C6,15.3137085 8.6862915,18 12,18 C15.3137085,18 18,15.3137085 18,12 C18,8.6862915 15.3137085,6 12,6 C10.6885336,6 9.44767246,6.42282109 8.43296491,7.17429118 Z" fill="#000000" fill-rule="nonzero"/>
                                        </g>
                                    </svg>
                                </button>
                            @endif
                        @endif

                        <div class="form-inline md-form filter-icon">
                            {{ Form::text(
                                'ADM_TASK_FILTER_SEARCH',
                                ($filter ?? ''),
                                array(
                                    'class'         => 'form-control filter',
                                    'placeholder'   => KJLocalization::translate('Algemeen', 'Zoeken', 'Zoeken') . '..',
                                    'id'            => 'ADM_TASK_FILTER_SEARCH'
                                )
                            ) }}
                        </div>

                            <div class="form-inline md-form ml-3">
                                {{ Form::select(
                                   'ADM_TASK_FILTER_ACTIVE',
                                        $status,
                                        ($filter_active ?? 1),
                                        [
                                            'class' => 'form-control filter kt-bootstrap-select',
                                            'id'            => 'ADM_TASK_FILTER_ACTIVE',
                                        ]
                                ) }}
                            </div>
                        @if($type == config('task_type.TYPE_PROJECT') || $type == config('task_type.TYPE_RELATION'))
                            <div class="kt-form__label" style=" margin-top: 7px; margin-left: 5px">
                                {{ Form::label('ADM_FILTER_PRODUCT_STATUS', KJLocalization::translate('Algemeen', 'Status', 'Status'). ':') }}
                            </div>
                            <div class="form-inline md-form ml-3">

                                {{ Form::select(
                                    'ADM_TASK_FILTER_STATUS',
                                    $taskStatus,
                                    ($filter_status ?? 1),
                                    [
                                        'class' => 'form-control filter kt-bootstrap-select',
                                        'id'            => 'ADM_TASK_FILTER_STATUS',
                                    ]
                                ) }}
                            </div>
                            <div class="kt-form__label" style=" margin-top: 7px; margin-left: 5px">
                                {{ Form::label('ADM_FILTER_TASK_FILTERS', KJLocalization::translate('Admin - Taken', 'Categorie', 'Categorie'). ':') }}
                            </div>
                            <div class="form-inline md-form ml-3">
                                {{ Form::select(
                                    'ADM_FILTER_TASK_FILTERS',
                                    $categories,
                                    \KJ\Core\libraries\SessionUtils::getSession('ADM_TASK', 'ADM_FILTER_TASK_FILTERS', 1),
                                    [
                                        'class' => 'form-control filter kt-bootstrap-select hasSessionState',
                                        'id'            => 'ADM_FILTER_TASK_FILTERS',
                                        'data-module'   => 'ADM_TASK',
                                        'data-key'      => 'ADM_FILTER_TASK_FILTERS'
                                    ]
                                ) }}
                            </div>
                        @endif
                    </div>
                </div>
                <div>
                    @if(!$blockEdit)
                        @if($type == config('task_type.TYPE_PROJECT') || $type == config('task_type.TYPE_PRODUCT'))
                            <a href="javascript:;" class="btn btn-success btn-sm btn-upper newStandardTaskList" data-id="-1" data-type="{{ $type }}" data-pid="{{ $pid }}" >
                                <i class="fa fa-plus-square"></i>
                                {{ KJLocalization::translate('Admin - Taken', 'Toevoegen uit standaard takenlijst', 'Toevoegen uit standaard takenlijst') }}
                            </a>
                        @endif
                        <a href="javascript:;" class="btn btn-success btn-sm btn-upper newTask" data-id="-1" data-type="{{ $type }}" data-pid="{{ $pid }}">
                            <i class="fa fa-plus-square"></i>
                            {{ KJLocalization::translate('Admin - Taken', 'Taak', 'Taak') }}
                        </a>
                    @endif
                </div>
            </div>

        </div>

        <div class="kt-todo__body">
            <div class="kt-todo__items">
                @foreach($items as $item)
                    <div class="kt-todo__item" data-title="{{ $item ? $item->SUBJECT : '' }}" data-type="task" id="searchItem">
                        @if($type != config('task_type.TYPE_TASKLIST') && $type != config('task_type.TYPE_PRODUCT') )
                            <div class="kt-todo__info">
                                <div class="kt-todo__actions">
                                    <label class="kt-checkbox kt-checkbox--single kt-checkbox--tick kt-checkbox--brand">
                                        <input type="checkbox" name="DONE" id="{{ $item ? $item->ID : 0 }}" class="exclude-screen-mode">
                                        <span></span>
                                    </label>
                                    @php($taskSub = $tasksSubs->where('FK_TASK', $item->ID)->first())
                                        <span class="kt-todo__icon kt-star kt-todo__icon--light {{ $taskSub ? 'kt-todo__icon--on' : '' }}" data-id="{{ $item->ID }}" data-subscription="{{ $taskSub->ID ?? 0 }}" data-toggle="kt-tooltip" data-placement="bottom" title="{{ KJLocalization::translate('Admin - Taken', 'Abonneren', 'Abonneren') }}">
                                            <i class="flaticon-star"></i>
                                        </span>
                                    @if((date('Y-m-d') > $item->DEADLINE) && ($item->DEADLINE != '') && ($item->DONE == false))
                                        <span class="kt-inbox__icon kt-inbox__icon--light ml-1" data-toggle="kt-tooltip" data-placement="bottom" data-original-title="{{ KJLocalization::translate('Admin - Taken', 'Taak verlopen', 'Taak verlopen') }}">
                                            <i class="fa fa-exclamation" style="color: #CE0012;"></i>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                        <div class="kt-todo__details" data-id="{{ $item ? $item->ID : 0 }}" data-toggle="view" data-type="{{$type}}">
                            <div class="kt-todo__message">
                                <div class="row">
                                    <div class="col mr-3">
                                        <span class="kt-todo__subject">
                                            {{ $item ? $item->SUBJECT : '' }}
                                            @if($item->CONTENT != '')
                                                &nbsp;-
                                            @endif
                                        </span>
                                        <span class="kt-todo__summary">
                                            {{ $item->getContentFormattedAttribute($item->CONTENT, 100) }}
                                        </span>
                                    </div>
                                    @if($type != config('task_type.TYPE_TASKLIST') && $type != config('task_type.TYPE_PRODUCT'))
                                        <div class="mr-3" style="width: 105px;">
                                            {{ $item->getDoneFormattedAttribute() }}
                                        </div>

                                        <div class="mr-3"  style="width: 130px;">
                                            {{ $item->getDeadlineDatePickerFormattedAttribute() }}
                                        </div>
                                    @else
                                        <div class="mr-3" style="width: 230px;">
                                            {{ $item->getDeadlineDaysFormattedAttribute() }}
                                        </div>
                                        <div class="mr-3" style="width: 230px;">
                                            {{ $item->getReminderDaysFormattedAttribute() }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="kt-todo__labels">
                                @if($type != config('task_type.TYPE_TASKLIST') && $type != config('task_type.TYPE_PRODUCT'))
                                    @if($item && $item->project_product)
                                        <span class="kt-todo__label kt-badge kt-badge--unified-warning kt-badge--bold kt-badge--inline">{{ $item->project_product->product->title }}</span>
                                    @endif

                                    @if($item && $item->assignee)
                                        <span class="kt-todo__label kt-badge kt-badge--unified-brand kt-badge--bold kt-badge--inline">{{ $item->assignee->FULLNAME }}</span>
                                    @else
                                        <span class="kt-todo__label kt-badge kt-badge--unified-danger kt-badge--bold kt-badge--inline">{{ KJLocalization::translate('Admin - Taken', 'Niet toegewezen', 'Niet toegewezen') }}</span>
                                    @endif

                                    @if($item->project && $type != config('task_type.TYPE_PROJECT'))
                                        <span class="kt-todo__label kt-badge kt-badge--unified-success kt-badge--bold kt-badge--inline">{{ $item->project->DESCRIPTION? $item->project->DESCRIPTION: $item->project->getTitleAttribute() }}</span>
                                    @endif
                                @endif
                                @if(count($item->categories) > 0)
                                    @foreach($item->categories as $category)
                                        <span class="kt-todo__label kt-badge kt-badge--unified-info kt-badge--bold kt-badge--inline">{{ $category->dropdownvalue->getValueAttribute() }}</span>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        @if(!$item->FK_TASK_LIST)
{{--                            <div class="kt-todo__datetime" data-toggle="view" >--}}
{{--                                {{ $item ? $item->getDeadlineFormattedAttribute() : '' }}--}}
{{--                            </div>--}}

                            <div class="kt-todo__sender" data-toggle="kt-tooltip" data-placement="top" data-original-title="{{ KJLocalization::translate('Admin - Taken', 'Aangemaakt door', 'Aangemaakt door') }}: {{ $item ? ($item->user_created ? $item->user_created->FULLNAME : '') : '' }}">
                                @if(($item->user_created->PHOTO ?? '') != '')
                                    <span class="kt-media kt-media--sm" style="background-image: url({{ asset((($item->user_created->PHOTO ?? '') != '') ? config('app.cdn_url') . $item->user_created->PHOTO : '/assets/theme/img/missing_logo_thumbnail.jpg') }})">
                                        <span></span>
                                    </span>
                                @else
                                    <span class="kt-media kt-media--sm kt-media--brand">
                                        <span>{{ $item ? $item->user_created->initialsByName() : '' }}</span>
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        @if($maxItems > $pageSize)
            @php($maxPage = ceil($maxItems / $pageSize))
            <div class="kt-todo__foot">
                <div class="kt-todo__toolbar">
                    <div class="kt-todo__controls">
                        <div class="kt-todo__pages">
                            <span class="kt-todo__perpage">{{ (($page - 1) * $pageSize) + 1 }} - {{ (($page - 1) * $pageSize) + 10 }} {{ KJLocalization::translate('Tabellen', 'van', 'van') }} {{ $maxItems }}</span>
                        </div>

                        @if($page > 1)
                            <button class="kt-todo__icon prevTaskPage" data-toggle="kt-tooltip" title="{{ KJLocalization::translate('Tabellen', 'Vorige pagina', 'Vorige pagina') }}" id="carOM-prev">
                                <i class="flaticon2-left-arrow"></i>
                            </button>
                        @endif

                        @if($page < $maxPage)
                            <button class="kt-todo__icon nextTaskPage" data-toggle="kt-tooltip" title="{{ KJLocalization::translate('Tabellen', 'Volgende pagina', 'Volgende pagina') }}"  id="carOM-prev">
                                <i class="flaticon2-right-arrow"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>