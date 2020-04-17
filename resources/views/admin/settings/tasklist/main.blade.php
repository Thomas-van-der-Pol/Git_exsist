@extends('theme.demo1.main', ['title' => KJLocalization::translate('Admin - Menu', 'Takenlijsten', 'Takenlijsten')])

@section('subheader')
    @component('breadcrums::main', [
        'rootURL'       => \KJ\Localization\libraries\LanguageUtils::getUrl('admin'),
        'parentTitle' => KJLocalization::translate('Admin - Menu', 'Takenlijsten', 'Takenlijsten'),
        'items' => [
            [
                'title' => KJLocalization::translate('Algemeen', 'Home', 'Home'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin'), '/')
            ],
            [
                'title' => KJLocalization::translate('Admin - Menu', 'Instellingen', 'Instellingen'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/group/1'), '/')
            ],
            [
                'title' => KJLocalization::translate('Admin - Menu', 'Takenlijsten', 'Takenlijsten'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/tasklist'), '/')
            ]
        ]
    ])
    @endcomponent
@endsection

@section('content')
    <div class="row">
        @component('portlet::main', ['title' => KJLocalization::translate('Admin - Menu', 'Takenlijsten', 'Takenlijsten'), 'colsize' => 12])

            @slot('headtools')
                <div class="kt-portlet__head-wrapper">
                    <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/tasklist/detail/-1') }}" id="newTasklist" class="btn btn-success btn-sm btn-upper">
                        <i class="fa fa-plus-square"></i>
                        {{ KJLocalization::translate('Admin - Tasklists', 'Takenlijst', 'Takenlijst')}}
                    </a>
                </div>
            @endslot

            <div class="kt-form kt-form--label-right">
                <div class="row align-items-center">
                    <div class="col-auto order-2 order-xl-1">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="kt-form__group kt-form__group--inline">
                                    <div class="kt-form__label">
                                        {{ Form::label('ADM_FILTER_TASKLIST_STATUS', KJLocalization::translate('Algemeen', 'Status', 'Status'). ':') }}
                                    </div>
                                    <div class="kt-form__control">
                                        {{ Form::select(
                                            'ADM_FILTER_TASKLIST_STATUS',
                                            $status,
                                            \KJ\Core\libraries\SessionUtils::getSession('ADM_TASKLIST', 'ADM_FILTER_TASKLIST_STATUS', 1),
                                            [
                                                'class' => 'form-control filter kt-bootstrap-select hasSessionState',
                                                'id'            => 'ADM_FILTER_TASKLIST_STATUS',
                                                'data-module'   => 'ADM_TASKLIST',
                                                'data-key'      => 'ADM_FILTER_TASKLIST_STATUS'
                                            ]
                                        ) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @slot('datatable')
                {{ KJDatatable::create(
                    'ADM_TASKLIST_TABLE',
                    array (
                        'method' => 'GET',
                        'url' => '/admin/settings/tasklist/allDatatable',
                        'editable' => true,
                        'editURL' => '/admin/settings/tasklist/detail/',
                        'editinline' => false,
                        'saveURL' => '/admin/settings/tasklist',
                        'columns' => array(
                            array(
                                'field' => 'NAME',
                                'title' => KJLocalization::translate('Algemeen', 'Naam', 'Naam')
                            )
                        ),
                        'filters' => array(
                            array(
                                'input' => '#ADM_FILTER_TASKLIST_STATUS',
                                'queryParam' => 'ACTIVE',
                                'default' => \KJ\Core\libraries\SessionUtils::getSession('ADM_TASKLIST', 'ADM_FILTER_TASKLIST_STATUS', 1)
                            )
                        )
                    )
                ) }}
            @endslot

        @endcomponent
    </div>
@endsection

@section('page-resources')

@endsection