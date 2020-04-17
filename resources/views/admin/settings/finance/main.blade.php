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
        @component('portlet::main', ['title' => KJLocalization::translate('Admin - Financieel', 'Administratie', 'Administratie'), 'colsize' => 6, 'portletClass' => 'kt-portlet--height-fluid'])
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

        @component('portlet::main', ['title' => KJLocalization::translate('Admin - Financieel', 'Indexatie', 'Indexatie'), 'colsize' => 6])
            @slot('headtools')
                <div class="kt-portlet__head-wrapper">
                    <a href="/admin/settings/finance/indexation/configure" class="btn btn-success btn-sm btn-upper" title="{{ KJLocalization::translate('Admin - Financieel', 'Indexeren', 'Indexeren') }}..">
                        <i class="m-menu__link-icon flaticon-diagram"></i>
                        {{ KJLocalization::translate('Admin - Financieel', 'Indexeren', 'Indexeren') }}
                    </a>
                    <a href="javascript:;" id="addIndexation" class="btn btn-success btn-sm ml-2 btn-upper">
                        <i class="fa fa-plus-square"></i>
                        {{ KJLocalization::translate('Admin - Financieel', 'Indexatie', 'Indexatie')}}
                    </a>
                </div>
            @endslot

            @slot('datatable')
                {{ KJDatatable::create(
                    'ADM_FINANCE_INDEXATION_TABLE',
                    array (
                        'method' => 'GET',
                        'url' => '/admin/settings/finance/indexation/allDatatable',
                        'editable' => true,
                        'editURL' => '/admin/settings/finance/indexation/detailRendered/',
                        'addable' => true,
                        'addButton' => '#addIndexation',
                        'saveURL' => '/admin/settings/finance/indexation',
                        'columns' => array(
                            array(
                                'field' => 'DESCRIPTION',
                                'title' => KJLocalization::translate('Admin - Financieel', 'Omschrijving', 'Omschrijving')
                            )
                        ),
                        'customEditButtons' => array(
                            'end' => [
                                [
                                    'HTML'  => '<a href="javascript:;" data-id="{idField}" class="btn btn-bold btn-label-danger btn-sm btn-icon deleteIndexation" title="' . KJLocalization::translate("Algemeen", "Verwijderen", "Verwijderen") . '" ><i class="la la-close""></i></a>'
                                ]
                            ]
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