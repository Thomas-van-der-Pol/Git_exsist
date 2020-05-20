@extends('theme.demo1.main', ['title' => KJLocalization::translate('Admin - Menu', 'Werknemers', 'Werknemers')])

@section('subheader')
    @component('breadcrums::main', [
        'rootURL'       => \KJ\Localization\libraries\LanguageUtils::getUrl('admin'),
        'parentTitle' => KJLocalization::translate('Admin - Menu', 'Werknemers', 'Werknemers'),
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
                'title' => KJLocalization::translate('Admin - Menu', 'Werknemers', 'Werknemers'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/user'), '/')
            ]
        ]
    ])
    @endcomponent
@endsection

@section('content')
    <div class="row">
        @component('portlet::main', ['title' => KJLocalization::translate('Admin - Menu', 'Werknemers', 'Werknemers'), 'colsize' => 12])
            @slot('headtools')
                <div class="kt-portlet__head-wrapper">
                    <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/user/detail/-1') }}" id="newUser" class="btn btn-success btn-sm btn-upper">
                        <i class="fa fa-plus-square"></i>
                        {{ KJLocalization::translate('Admin - Werknemers', 'Werknemer', 'Werknemer')}}
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
                                        'ADM_FILTER_USER',
                                        \KJ\Core\libraries\SessionUtils::getSession('ADM_USER', 'ADM_FILTER_USER', ''),
                                        array(
                                            'class'         => 'form-control filter hasSessionState',
                                            'placeholder'   => KJLocalization::translate('Algemeen', 'Zoeken', 'Zoeken').'...',
                                            'id'            => 'ADM_FILTER_USER',
                                            'data-module'   => 'ADM_USER',
                                            'data-key'      => 'ADM_FILTER_USER'
                                        )
                                    ) }}
                                </div>
                            </div>

                            <div class="col-2">
                                <div class="kt-form__group kt-form__group--inline">
                                    <div class="kt-form__label">
                                        {{ Form::label('ADM_FILTER_USER_STATUS', KJLocalization::translate('Algemeen', 'Status', 'Status'). ':') }}
                                    </div>
                                    <div class="kt-form__control">
                                        {{ Form::select(
                                            'ADM_FILTER_USER_STATUS',
                                            $status,
                                            \KJ\Core\libraries\SessionUtils::getSession('ADM_USER', 'ADM_FILTER_USER_STATUS', 1),
                                            [
                                                'class' => 'form-control filter kt-bootstrap-select hasSessionState',
                                                'id'            => 'ADM_FILTER_USER_STATUS',
                                                'data-module'   => 'ADM_USER',
                                                'data-key'      => 'ADM_FILTER_USER_STATUS'
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
                    'ADM_USER_TABLE',
                    array (
                        'method' => 'GET',
                        'url' => '/admin/settings/user/allDatatable',
                        'editable' => true,
                        'editURL' => '/admin/settings/user/detail/',
                        'editinline' => false,
                        'addable' => false,
                        'addButton' => '#newUser',
                        'searchinput' => '#ADM_FILTER_USER',
                        'saveURL' => '/admin/settings/user',
                        'columns' => array(
                            array(
                                'field' => 'FULLNAME',
                                'title' => KJLocalization::translate('Admin - Werknemers', 'Naam', 'Naam')
                            ),
                            array(
                                'field' => 'EMAILADDRESS',
                                'title' => KJLocalization::translate('Admin - Werknemers', 'E-mailadres', 'E-mailadres')
                            ),
                            array(
                                'field' => 'PHONE_MOBILE',
                                'title' => KJLocalization::translate('Admin - Werknemers', 'Mobiel', 'Mobiel')
                            ),
                            array(
                                'field' => 'PHONE_EMERGENCY',
                                'title' => KJLocalization::translate('Admin - Werknemers', 'Telefoon noodgeval', 'Telefoon noodgeval')
                            )
                        ),
                        'filters' => array(
                            array(
                                'input' => '#ADM_FILTER_USER_STATUS',
                                'queryParam' => 'ACTIVE',
                                'default' => \KJ\Core\libraries\SessionUtils::getSession('ADM_USER', 'ADM_FILTER_USER_STATUS', 1)
                            )
                        )
                    )
                ) }}
            @endslot

        @endcomponent
    </div>  
@endsection

@section('page-resources')
    {!! Html::script('/assets/custom/js/admin/setting/user/main.js?v='.Cache::get('cache_version_number')) !!}
@endsection