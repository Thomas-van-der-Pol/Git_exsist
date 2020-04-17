@extends('theme.demo1.main', ['title' => $item ? $item->SUBJECT : KJLocalization::translate('Admin - Taken', 'Nieuwe taak', 'Nieuwe taak')])

@section('subheader')
    @php
        $breadCrumsTaskList = [];
        $breadCrumsTaskList[] = [
            'title' => KJLocalization::translate('Algemeen', 'Home', 'Home'),
            'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin'), '/')
        ];

        if($item->FK_TASK_LIST) {
            $breadCrumsTaskList[] = [
                'title' => KJLocalization::translate('Admin - Menu', 'Instellingen', 'Instellingen'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/group/1'), '/')
            ];
            $breadCrumsTaskList[] = [
                'title' => KJLocalization::translate('Admin - Menu', 'Takenlijsten', 'Takenlijsten'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/tasklist'), '/')
            ];
            $breadCrumsTaskList[] = [
                'title' => $item ? $item->taskList->NAME : KJLocalization::translate('Admin - Tasklists', 'Nieuwe Takenlijst', 'Nieuwe Takenlijst'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/tasklist/detail/' . ($item->taskList->ID)), '/')
            ];
            $breadCrumsTaskList[] = [
                'title' => KJLocalization::translate('Admin - Menu', 'Taken', 'Taken'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/tasklist/detail/' . ($item->taskList->ID)), '/')
            ];
        }
        else if ($item->FK_PROJECT) {
            $breadCrumsTaskList[] = [
                'title' => KJLocalization::translate('Admin - Menu', 'Dossiers', 'Dossiers'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/project'), '/')
            ];
            $breadCrumsTaskList[] = [
                'title' => $item ? ($item->project->DESCRIPTION? $item->project->DESCRIPTION : $item->project->getTitleAttribute()) : KJLocalization::translate('Admin - Dossiers', 'Nieuwe dossier', 'Nieuwe dossier'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/project/detail/' . ($item->project->ID)), '/')
            ];
            $breadCrumsTaskList[] = [
                'title' => KJLocalization::translate('Admin - Menu', 'Taken', 'Taken'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/project/detail/' . ($item->project->ID)), '/')
            ];
        }
        else if ($item->FK_CRM_RELATION) {
            $breadCrumsTaskList[] = [
                'title' => KJLocalization::translate('Admin - Menu', 'CRM Relaties', 'CRM Relaties'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/crm/relation'), '/')
            ];
            $breadCrumsTaskList[] = [
                'title' => $item ? $item->relation->getTitleAttribute() : KJLocalization::translate('Admin - Relaties', 'Nieuwe relatie', 'Nieuwe relatie'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/crm/relation/detail/' . ($item->relation->ID)), '/')
            ];
            $breadCrumsTaskList[] = [
                'title' => KJLocalization::translate('Admin - Menu', 'Taken', 'Taken'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/crm/relation/detail/' . ($item->relation->ID)), '/')
            ];
        }
        else {
            $breadCrumsTaskList[] = [
                'title' => KJLocalization::translate('Admin - Menu', 'Taken', 'Taken'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/tasks'), '/')
            ];
        }

        $breadCrumsTaskList[] = [
            'title' => $item ? $item->SUBJECT : KJLocalization::translate('Admin - Taken', 'Nieuwe taak', 'Nieuwe taak'),
            'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/tasks/detail/'. ( $item ? $item->ID : -1 )), '/')
        ];
    @endphp
    @component('breadcrums::main', [
        'rootURL'       => \KJ\Localization\libraries\LanguageUtils::getUrl('admin'),
        'parentTitle' => KJLocalization::translate('Admin - Menu', 'Taken', 'Taken'),
        'items' => $breadCrumsTaskList
    ])
    @endcomponent
@endsection

@section('content')
    <div class="row">
        @component('portlet::main', ['notitle' => true, 'colsize' => 12, 'portletClass' => 'kt-portlet--height-fluid'])
            <div class="kt-widget kt-widget--user-profile-3" id="default" data-id="{{ ( $item ? $item->ID : -1 ) }}">

            </div>
        @endcomponent
    </div>
@endsection

@section('page-resources')
    {!! Html::script('/assets/custom/js/admin/tasks/detail.js?v='.Cache::get('cache_version_number')) !!}
@endsection