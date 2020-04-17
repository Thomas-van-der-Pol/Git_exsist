@extends('theme.demo1.main', ['title' => KJLocalization::translate('Admin - Menu', 'Dossiers', 'Dossiers')])

@section('subheader')
    @component('breadcrums::main', [
        'rootURL'       => \KJ\Localization\libraries\LanguageUtils::getUrl('admin'),
        'parentTitle' => KJLocalization::translate('Admin - Menu', 'Dossiers', 'Dossiers'),
        'items' => [
            [
                'title' => KJLocalization::translate('Algemeen', 'Home', 'Home'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin'), '/')
            ],
            [
                'title' => KJLocalization::translate('Admin - Menu', 'Dossiers', 'Dossiers'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/project'), '/')
            ]
        ]
    ])
    @endcomponent
@endsection

@section('content')
    <div class="row">
        @component('portlet::main', ['notitle' => true, 'colsize' => 2])
            @php($currentTab = \KJ\Core\libraries\SessionUtils::getSession('ADM_PROJECT', 'CURRENT_TAB', array_keys($project_types)[0]))

            <div class="kt-widget kt-widget--user-profile-1 pb-0">
                <div class="kt-widget__body">
                    <div class="kt-widget__items nav" role="tablist">

                        @foreach($project_types as $id => $description)
                            <a href="#projects_{{ $id }}" class="kt-widget__item {{ ($currentTab == $id) ? 'kt-widget__item--active' : '' }}" data-toggle="tab" role="tab">
                                <span class="kt-widget__section">
                                    <span class="kt-widget__desc">
                                        {{ $description }}
                                    </span>
                                </span>
                            </a>
                        @endforeach

                    </div>
                </div>
            </div>
        @endcomponent

        <div class="col-lg-10">
            <div class="kt-portlet kt-portlet--mobile" id="ASSIGNEE_FILTER">
                <div class="kt-portlet__body">
                    <div class="kt-form kt-form--label-right">
                        <div class="row align-items-center">
                            <div class="col order-xl-1">
                                <div class="row align-items-center">

                                    <div class="col-auto">
                                        <div class="form-inline md-form filter-icon">
                                            {{ Form::text(
                                                'ADM_PROJECT_FILTER_SEARCH',
                                                \KJ\Core\libraries\SessionUtils::getSession('ADM_PROJECT', 'ADM_PROJECT_FILTER_SEARCH', ''),
                                                array(
                                                    'class'         => 'form-control filter hasSessionState',
                                                    'placeholder'   => KJLocalization::translate('Algemeen', 'Zoeken', 'Zoeken') . '..',
                                                    'id'            => 'ADM_PROJECT_FILTER_SEARCH',
                                                    'data-module'   => 'ADM_PROJECT',
                                                    'data-key'      => 'ADM_PROJECT_FILTER_SEARCH'
                                                )
                                            ) }}
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <div class="kt-form__group kt-form__group--inline">
                                            <div class="kt-form__label">
                                                {{ Form::label('ADM_FILTER_PROJECT_STATUS', KJLocalization::translate('Algemeen', 'Status', 'Status'). ':') }}
                                            </div>
                                            <div class="kt-form__control">
                                                {{ Form::select(
                                                    'ADM_FILTER_PROJECT_STATUS',
                                                    $status,
                                                    \KJ\Core\libraries\SessionUtils::getSession('ADM_PROJECT', 'ADM_FILTER_PROJECT_STATUS', 1),
                                                    [
                                                        'class' => 'form-control filter kt-bootstrap-select hasSessionState',
                                                        'id'            => 'ADM_FILTER_PROJECT_STATUS',
                                                        'data-module'   => 'ADM_PROJECT',
                                                        'data-key'      => 'ADM_FILTER_PROJECT_STATUS'
                                                    ]
                                                ) }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-3">
                                        <div class="kt-form__group kt-form__group--inline">
                                            <div class="kt-checkbox-inline">
                                                <label class="kt-checkbox">
                                                    <input name="ADM_FILTER_PROJECT_SHOW_DONE" type="checkbox" value="1" id="ADM_FILTER_PROJECT_SHOW_DONE">
                                                    {{ KJLocalization::translate('Admin - Dossiers', 'Toon afgeronde dossiers', 'Toon afgeronde dossiers') }}
                                                    <span></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if(Auth::guard()->user()->hasPermission(config('permission.DOSSIERS_TOEVOEGEN')))
                                <div class="col-auto order-2 order-xl-2">
                                    <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/project/detail/-1') }}" id="newClient" class="btn btn-success btn-sm btn-upper ml-2 pull-right">
                                        <i class="fa fa-plus-square"></i>
                                        {{ KJLocalization::translate('Admin - Dossiers', 'Dossier', 'Dossier') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="kt-portlet kt-portlet--mobile" id="detailScreenContainer">
                <div class="kt-portlet__body kt-portlet__body--fit">
                    <div class="tab-content">

                        @foreach($project_types as $id => $description)
                            <div class="tab-pane {{ ($currentTab == $id) ? 'active' : '' }}" id="projects_{{ $id }}" data-id="-1" data-type="{{ $id }}" role="tabpanel"></div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('page-resources')
    {!! Html::script('/assets/custom/js/admin/project/main.js?v='.Cache::get('cache_version_number')) !!}
@endsection