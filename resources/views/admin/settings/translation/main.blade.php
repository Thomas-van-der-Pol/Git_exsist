@extends('theme.demo1.main', ['title' => KJLocalization::translate('Admin - Menu', 'Translations', 'Translations')])

@section('subheader')
    @component('breadcrums::main', [
        'rootURL'       => \KJ\Localization\libraries\LanguageUtils::getUrl('admin'),
        'parentTitle'   => KJLocalization::translate('Admin - Menu', 'Translations', 'Translations'),
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
                'title' => KJLocalization::translate('Admin - Menu', 'Translations', 'Translations'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/translation'), '/')
            ]
        ]
    ])
    @endcomponent
@endsection

@section('content')
    @include('localization::main')
@endsection

@section('page-resources')
    {!! Html::script('assets/kj/localization/management/main.js?v='.Cache::get('cache_version_number')) !!}
@endsection