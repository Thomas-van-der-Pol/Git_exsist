@extends('theme.demo1.main', ['title' => KJLocalization::translate('Admin - Menu', 'Workflows', 'Workflows')])

@section('subheader')
    @component('breadcrums::main', [
        'rootURL'       => \KJ\Localization\libraries\LanguageUtils::getUrl('admin'),
        'parentTitle' => KJLocalization::translate('Admin - Menu', 'Workflows', 'Workflows'),
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
                'title' => KJLocalization::translate('Admin - Menu', 'Workflows', 'Workflows'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/workflow'), '/')
            ]
        ]
    ])
    @endcomponent
@endsection

@section('content')
    <div class="row">
        @component('portlet::main', ['title' => KJLocalization::translate('Admin - Menu', 'Workflows', 'Workflows'), 'colsize' => 12])

            @slot('headtools')
                <div class="kt-portlet__head-wrapper">
                    <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/workflow/detail/-1') }}" id="newWorkflow" class="btn btn-success btn-sm btn-upper">
                        <i class="fa fa-plus-square"></i>
                        {{ KJLocalization::translate('Admin - Workflows', 'Workflow', 'Workflow')}}
                    </a>
                </div>
            @endslot

            @slot('datatable')
                {{ KJDatatable::create(
                    'ADM_WORKFLOW_TABLE',
                    array (
                        'method' => 'GET',
                        'url' => '/admin/settings/workflow/allDatatable',
                        'editable' => true,
                        'editURL' => '/admin/settings/workflow/detail/',
                        'editinline' => false,
                        'saveURL' => '/admin/settings/workflow',
                        'columns' => array(
                            array(
                                'field' => 'DESCRIPTION',
                                'title' => KJLocalization::translate('Admin - Workflows', 'Omschrijving', 'Omschrijving')
                            ),
                            array(
                                'field' => 'PROJECT_TYPE',
                                'title' => KJLocalization::translate('Admin - Workflows', 'Projecttype', 'Projecttype')
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