@extends('theme.demo1.main', ['title' => KJLocalization::translate('Admin - Menu', 'Financieel', 'Financieel')])

@section('subheader')
    @component('breadcrums::main', [
        'rootURL'       => \KJ\Localization\libraries\LanguageUtils::getUrl('admin'),
        'parentTitle' => KJLocalization::translate('Admin - Menu', 'Financieel', 'Financieel'),
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
                'title' => KJLocalization::translate('Admin - Menu', 'Financieel', 'Financieel'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/finance'), '/')
            ]
        ]
    ])
    @endcomponent
@endsection

@section('content')
    <div class="row">
        @component('portlet::main', ['title' => KJLocalization::translate('Admin - Financieel', 'Administratie', 'Administratie'), 'colsize' => 12, 'portletClass' => 'kt-portlet--height-fluid'])
            @slot('datatable')
                {{ KJDatatable::create(
                    'ADM_FINANCE_LABEL_TABLE',
                    array (
                        'method' => 'GET',
                        'url' => '/admin/settings/finance/allDatatable',
                        'editable' => true,
                        'editURL' => '/admin/settings/finance/detail/',
                        'editinline' => false,
                        'columns' => array(
                            array(
                                'field' => 'DESCRIPTION',
                                'title' => KJLocalization::translate('Admin - Financieel', 'Omschrijving', 'Omschrijving')
                            )
                        )
                    )
                ) }}
            @endslot
        @endcomponent
    </div>
@endsection

@section('page-resources')
    {!! Html::script('/assets/custom/js/admin/setting/finance/main.js?v='.Cache::get('cache_version_number')) !!}
@endsection