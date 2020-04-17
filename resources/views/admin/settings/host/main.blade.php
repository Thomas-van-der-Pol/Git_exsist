@extends('theme.demo1.main', ['title' => KJLocalization::translate('Admin - Menu', 'Werkstations', 'Werkstations')])

@section('subheader')
    @component('breadcrums::main', [
        'rootURL'       => \KJ\Localization\libraries\LanguageUtils::getUrl('admin'),
        'parentTitle' => KJLocalization::translate('Admin - Menu', 'Werkstations', 'Werkstations'),
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
                'title' => KJLocalization::translate('Admin - Menu', 'Werkstations', 'Werkstations'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/host'), '/')
            ]
        ]
    ])
    @endcomponent
@endsection

@section('content')
    <div class="row">
        @component('portlet::main', ['title' => KJLocalization::translate('Admin - Menu', 'Werkstations', 'Werkstations'), 'colsize' => 12])
            @slot('headtools')
                <div class="kt-portlet__head-wrapper">
                    <a href="javascript:;" id="newHost" class="btn btn-success btn-sm btn-upper" title="{{ KJLocalization::translate('Admin - Werkstations', 'Nieuw werkstation', 'Nieuw werkstation')  }}..">
                        <i class="fa fa-plus-square"></i>
                        {{ KJLocalization::translate('Admin - Werkstations', 'Werkstation', 'Werkstation')  }}
                    </a>
                </div>
            @endslot

            @slot('datatable')
                {{ KJDatatable::create(
                    'host_table',
                    [
                        'method' => 'GET',
                        'url' => '/admin/settings/host/allDatatable',
                        'editable' => true,
                        'editURL' => '/admin/settings/host/detailRendered/',
                        'addable' => true,
                        'addButton' => '#newHost',
                        'saveURL' => '/admin/settings/host',
                        'columns' => [
                            [
                                'field' => 'HOSTNAME',
                                'title' => KJLocalization::translate('Admin - Werkstations', 'Werkstation', 'Werkstation')
                            ],
                            [
                                'field' => 'MAC_ADDRESS',
                                'title' => KJLocalization::translate('Admin - Werkstations', 'MAC', 'MAC-adres')
                            ],
                            [
                                'field' => 'PRINTER_DEFAULT',
                                'title' => KJLocalization::translate('Admin - Werkstations', 'Standaard printer', 'Standaard printer')
                            ],
                            [
                                'field' => 'PRINTER_INVOICE',
                                'title' => KJLocalization::translate('Admin - Werkstations', 'Factuur printer', 'Factuur printer')
                            ]
                        ]
                    ]
                ) }}
            @endslot
        @endcomponent
    </div>  
@endsection

@section('page-resources')
    {!! Html::script('/assets/custom/js/admin/setting/host/main.js?v='.Cache::get('cache_version_number')) !!}
@endsection