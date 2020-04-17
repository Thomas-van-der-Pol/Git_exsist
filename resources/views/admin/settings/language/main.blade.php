@extends('theme.demo1.main', ['title' => KJLocalization::translate('Admin - Menu', 'Language', 'Language')])

@section('subheader')
    @component('breadcrums::main', [
        'rootURL'       => \KJ\Localization\libraries\LanguageUtils::getUrl('admin'),
        'parentTitle' => KJLocalization::translate('Admin - Menu', 'Language', 'Language'),
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
                'title' => KJLocalization::translate('Admin - Menu', 'Language', 'Language'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/language'), '/')
            ]
        ]
    ])
    @endcomponent
@endsection

@section('content')
    <div class="row">
        @component('portlet::main', ['title' => KJLocalization::translate('Admin - Menu', 'Language', 'Language'), 'colsize' => 12])

              @slot('datatable')
                {{ KJDatatable::create(
                    'ADM_LANGUAGE_TABLE',
                    array (
                        'method' => 'GET',
                        'url' => '/admin/settings/language/allDatatable',
                        'editable' => true,
                        'editURL' => '/admin/settings/language/detailRendered/',
                        'addable' => false,
                        'saveURL' => '/admin/settings/language',
                        'columns' => array(
                            array(
                                'field' => 'DESCRIPTION',
                                'title' => KJLocalization::translate('Admin - Language', 'Language', 'Language')
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