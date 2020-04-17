@extends('theme.demo1.main', ['title' => KJLocalization::translate('Admin - Menu', 'Relaties', 'Relaties')])

@section('subheader')
    @component('breadcrums::main', [
        'rootURL'       => \KJ\Localization\libraries\LanguageUtils::getUrl('admin'),
        'parentTitle' => KJLocalization::translate('Admin - Menu', 'Relaties', 'Relaties'),
        'items' => [
            [
                'title' => KJLocalization::translate('Algemeen', 'Home', 'Home'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin'), '/')
            ],
            [
                'title' => KJLocalization::translate('Admin - Menu', 'CRM Relaties', 'CRM Relaties'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/crm/relation'), '/')
            ]
        ]
    ])
    @endcomponent
@endsection

@section('content')
    <div class="row">
        @component('portlet::main', ['title' => KJLocalization::translate('Admin - Menu', 'Relaties', 'Relaties'), 'colsize' => 12])
            @slot('headicon')
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon id="Shape" points="0 0 24 0 24 24 0 24"></polygon>
                        <path d="M18,14 C16.3431458,14 15,12.6568542 15,11 C15,9.34314575 16.3431458,8 18,8 C19.6568542,8 21,9.34314575 21,11 C21,12.6568542 19.6568542,14 18,14 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" id="Combined-Shape" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                        <path d="M17.6011961,15.0006174 C21.0077043,15.0378534 23.7891749,16.7601418 23.9984937,20.4 C24.0069246,20.5466056 23.9984937,21 23.4559499,21 L19.6,21 C19.6,18.7490654 18.8562935,16.6718327 17.6011961,15.0006174 Z M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" id="Combined-Shape" fill="#000000" fill-rule="nonzero"></path>
                    </g>
                </svg>
            @endslot

            @slot('headtools')
                <div class="kt-portlet__head-wrapper">
                    <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/crm/relation/detail/-1') }}" id="newRelation" class="btn btn-success btn-sm btn-upper">
                        <i class="fa fa-plus-square"></i>
                        {{ KJLocalization::translate('Admin - CRM', 'Relatie', 'Relatie')}}
                    </a>
                </div>
            @endslot

            <div class="kt-form kt-form--label-right">
                <div class="row align-items-center">
                    <div class="col-auto order-2 order-xl-1">
                        <div class="row align-items-center">

                            <div class="col-auto">
                                <div class="form-inline md-form filter-icon">
                                    {{ Form::text(
                                        'ADM_RELATION_FILTER_SEARCH',
                                        \KJ\Core\libraries\SessionUtils::getSession('ADM_RELATION', 'ADM_RELATION_FILTER_SEARCH', ''),
                                        array(
                                            'class'         => 'form-control filter hasSessionState',
                                            'placeholder'   => KJLocalization::translate('Algemeen', 'Zoeken', 'Zoeken') . '..',
                                            'id'            => 'ADM_RELATION_FILTER_SEARCH',
                                            'data-module'   => 'ADM_RELATION',
                                            'data-key'      => 'ADM_RELATION_FILTER_SEARCH'
                                        )
                                    ) }}
                                </div>
                            </div>

                            <div class="col-auto">
                                <div class="kt-form__group kt-form__group--inline">
                                    <div class="kt-form__label">
                                        {{ Form::label('ADM_FILTER_RELATION_STATUS', KJLocalization::translate('Algemeen', 'Status', 'Status'). ':') }}
                                    </div>
                                    <div class="kt-form__control">
                                        {{ Form::select(
                                            'ADM_FILTER_RELATION_STATUS',
                                            $status,
                                            \KJ\Core\libraries\SessionUtils::getSession('ADM_RELATION', 'ADM_FILTER_RELATION_STATUS', 1),
                                            [
                                                'class' => 'form-control filter kt-bootstrap-select hasSessionState',
                                                'id'            => 'ADM_FILTER_RELATION_STATUS',
                                                'data-module'   => 'ADM_RELATION',
                                                'data-key'      => 'ADM_FILTER_RELATION_STATUS'
                                            ]
                                        ) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="kt-form__group kt-form__group--inline">
                                    <div class="kt-form__label">
                                        {{ Form::label('ADM_FILTER_RELATION_TYPE', KJLocalization::translate('Algemeen', 'Type', 'Type'). ':') }}
                                    </div>
                                    <div class="kt-form__control">
                                        {{ Form::select(
                                            'ADM_FILTER_RELATION_TYPE',
                                            $relationtypes,
                                            \KJ\Core\libraries\SessionUtils::getSession('ADM_RELATION', 'ADM_FILTER_RELATION_TYPE', ''),
                                            [
                                                'class' => 'form-control filter kt-bootstrap-select hasSessionState',
                                                'id'            => 'ADM_FILTER_RELATION_TYPE',
                                                'data-module'   => 'ADM_RELATION',
                                                'data-key'      => 'ADM_FILTER_RELATION_TYPE'
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
                    'ADM_RELATION_TABLE',
                    array (
                        'method' => 'GET',
                        'url' => '/admin/crm/relation/allDatatable',
                        'editable' => true,
                        'editinline' => false,
                        'editURL' => \KJ\Localization\libraries\LanguageUtils::getUrl('admin/crm/relation/detail/'),
                        'addable' => false,
                        'pagination' => true,
                        'sortable' => true,
                        'searchinput' => '#ADM_RELATION_FILTER_SEARCH',
                        'columns' => array(
                            array(
                                'field' => 'NAME',
                                'title' => KJLocalization::translate('Admin - CRM', 'Naam', 'Naam')
                            ),
                            array(
                                'field' => 'RELATION_TYPE',
                                'title' => KJLocalization::translate('Admin - CRM', 'Type', 'Type')
                            ),
                            array(
                                'field' => 'EMAILADDRESS',
                                'title' => KJLocalization::translate('Admin - CRM', 'E-mailadres', 'E-mailadres')
                            )
                        ),
                        'filters' => array(
                            array(
                                'input' => '#ADM_FILTER_RELATION_STATUS',
                                'queryParam' => 'ACTIVE',
                                'default' => \KJ\Core\libraries\SessionUtils::getSession('ADM_RELATION', 'ADM_FILTER_RELATION_STATUS', 1)
                            ),
                            array(
                                'input' => '#ADM_FILTER_RELATION_TYPE',
                                'queryParam' => 'FK_CORE_DROPDOWNVALUE_RELATIONTYPE',
                                'default' => \KJ\Core\libraries\SessionUtils::getSession('ADM_RELATION', 'ADM_FILTER_RELATION_TYPE', '')
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