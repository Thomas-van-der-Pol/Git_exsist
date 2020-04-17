@extends('theme.demo1.main', ['title' => KJLocalization::translate('Admin - Menu', 'Rollen & rechten', 'Rollen & rechten')])

@section('subheader')
    @component('breadcrums::main', [
        'rootURL'       => \KJ\Localization\libraries\LanguageUtils::getUrl('admin'),
        'parentTitle' => KJLocalization::translate('Admin - Menu', 'Rollen & rechten', 'Rollen & rechten'),
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
                'title' => KJLocalization::translate('Admin - Menu', 'Rollen & rechten', 'Rollen & rechten'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/role'), '/')
            ]
        ]
    ])
    @endcomponent
@endsection

@section('content')
    <div class="row">
        @component('portlet::main', ['title' => KJLocalization::translate('Admin - Menu', 'Rollen & rechten', 'Rollen & rechten'), 'colsize' => 12])
            @slot('headtools')
                <div class="kt-portlet__head-wrapper">
                    <a href="javascript:;" id="newRole" class="btn btn-success btn-sm btn-upper">
                        <i class="fa fa-plus-square"></i>
                        {{ KJLocalization::translate('Admin - Rollen & rechten', 'Rol', 'Rol')}}
                    </a>
                </div>
            @endslot

            <div class="kt-form kt-form--label-right">
                <div class="row align-items-center">
                    <div class="col-lg-12 order-2 order-xl-1">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="form-inline md-form filter-icon">
                                    {{ Form::text(
                                        'ADM_FILTER_ROLE',
                                        \KJ\Core\libraries\SessionUtils::getSession('ADM_ROLE', 'ADM_FILTER_ROLE', ''),
                                        array(
                                            'class'         => 'form-control filter hasSessionState',
                                            'placeholder'   => KJLocalization::translate('Algemeen', 'Zoeken', 'Zoeken').'...',
                                            'id'            => 'ADM_FILTER_ROLE',
                                            'data-module'   => 'ADM_ROLE',
                                            'data-key'      => 'ADM_FILTER_ROLE'
                                        )
                                    ) }}
                                </div>
                            </div>

                            <div class="col-2">
                                <div class="kt-form__group kt-form__group--inline">
                                    <div class="kt-form__label">
                                        {{ Form::label('ADM_FILTER_ROLE_STATUS', KJLocalization::translate('Algemeen', 'Status', 'Status'). ':') }}
                                    </div>
                                    <div class="kt-form__control">
                                        {{ Form::select(
                                            'ADM_FILTER_ROLE_STATUS',
                                            $status,
                                            \KJ\Core\libraries\SessionUtils::getSession('ADM_ROLE', 'ADM_FILTER_ROLE_STATUS', 1),
                                            [
                                                'class' => 'form-control filter kt-bootstrap-select hasSessionState',
                                                'id'            => 'ADM_FILTER_ROLE_STATUS',
                                                'data-module'   => 'ADM_ROLE',
                                                'data-key'      => 'ADM_FILTER_ROLE_STATUS'
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
                <div class="kt-separator m-0"></div>

                {{ KJDatatable::create(
                    'ADM_ROLE_TABLE',
                    array (
                        'method' => 'GET',
                        'url' => '/admin/settings/role/allDatatable',
                        'editable' => true,
                        'editURL' => '/admin/settings/role/detailRendered/',
                        'addable' => true,
                        'addButton' => '#newRole',
                        'searchinput' => '#ADM_FILTER_ROLE',
                        'saveURL' => '/admin/settings/role',
                        'columns' => array(
                            array(
                                'field' => 'DESCRIPTION',
                                'title' => KJLocalization::translate('Admin - Rollen & rechten', 'Rol', 'Rol')
                            )
                        ),
                        'filters' => array(
                            array(
                                'input' => '#ADM_FILTER_ROLE_STATUS',
                                'queryParam' => 'ACTIVE',
                                'default' => \KJ\Core\libraries\SessionUtils::getSession('ADM_RELATION', 'ADM_FILTER_CLIENT_STATUS', 1)
                            )
                        ),
                        'customEditButtons' => array(
                            'end' => [
                                [
                                    'HTML'  => '<a href="javascript:;" data-id="{idField}" class="btn btn-bold btn-label-danger btn-sm btn-icon deleteRole" title="' . KJLocalization::translate("Algemeen", "Archiveren", "Archiveren") . '" ><i class="la la-close""></i></a>'
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
    {!! Html::script('/assets/custom/js/admin/setting/role/main.js?v='.Cache::get('cache_version_number')) !!}
@endsection