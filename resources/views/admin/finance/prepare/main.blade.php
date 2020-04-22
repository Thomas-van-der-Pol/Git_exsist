@extends('theme.demo1.main', ['title' => KJLocalization::translate('Admin - Menu', 'Facturatie voorbereiden', 'Facturatie voorbereiden')])

@section('subheader')
    @component('breadcrums::main', [
        'rootURL'       => \KJ\Localization\libraries\LanguageUtils::getUrl('admin'),
        'parentTitle' => KJLocalization::translate('Admin - Menu', 'Facturatie voorbereiden', 'Facturatie voorbereiden'),
        'items' => [
            [
                'title' => KJLocalization::translate('Algemeen', 'Home', 'Home'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin'), '/')
            ],
            [
                'title' => KJLocalization::translate('Admin - Menu', 'Facturen', 'Facturen'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/invoice'), '/')
            ],
            [
                'title' => KJLocalization::translate('Admin - Menu', 'Facturatie voorbereiden', 'Facturatie voorbereiden'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/invoice/prepare'), '/')
            ]
        ]
    ])
    @endcomponent
@endsection

@section('content')
    <div class="row">
        @component('portlet::main', ['notitle' => true, 'colsize' => 12])
            <div class="row">
                <div class="col-lg-2">
                    {{ KJField::date('REFERENCE_DATE', KJLocalization::translate('Admin - Facturen', 'Peildatum', 'Peildatum'), date(\KJ\Localization\libraries\LanguageUtils::getDateFormat()), ['required'], true) }}

                    {{ KJField::saveCancel(
                        'btnUpdateData',
                        '',
                        true,
                        array(
                            'removeCancel' => true,
                            'saveText' => KJLocalization::translate('Admin - Facturen', 'Actualiseren', 'Actualiseren')
                        )
                    ) }}
                </div>
            </div>
        @endcomponent
    </div>

    <div class="row">
        @component('portlet::main', ['notitle' => true, 'colsize' => 12])
            <div class="kt-form kt-form--label-right">
                <div class="row align-items-center">
                    <div class="col order-1 order-xl-1">
                        <a href="javascript:;" id="createInvoices" class="btn btn-warning btn-sm btn-upper pull-right">
                            <i class="fa fa-money-check-alt"></i>
                            {{ KJLocalization::translate('Admin - Facturen', 'Maak conceptfacturen', 'Maak conceptfacturen')}}
                        </a>
                    </div>
                </div>
            </div>

            @slot('datatable')
                {{ KJDatatable::create(
                    'ADM_BILLCHECK_TABLE',
                    array (
                        'method' => 'GET',
                        'url' => '/admin/invoice/prepare/allDatatable',
                        'pagination' => false,
                        'sortable' => false,
                        'checkable' => true,
                        'checkableDescriptionColumn' => 'RELATION',
                        'editable' => true,
                        'editinline' => true,
                        'customID' => 'BILLCHECKIDString',
                        'editURL' => '/admin/invoice/prepare/detail/',
                        'pagesize' => 99999,
                        'columns' => array(
                            array(
                                'field' => 'RELATION',
                                'title' => KJLocalization::translate('Admin - Facturen', 'Relatie', 'Relatie'),
                            ),
                            array(
                                'field' => 'WORKFLOWSTATE',
                                'title' => KJLocalization::translate('Admin - Facturen', 'Dossierstatus', 'Dossierstatus'),
                            ),
                            array(
                                'field' => 'PRICE_TOTAL_FORMATTED',
                                'title' => KJLocalization::translate('Admin - Facturen', 'Bedrag excl', 'Bedrag excl.'),
                            ),
                            array(
                                'field' => 'PRICE_TOTAL_INCVAT_FORMATTED',
                                'title' => KJLocalization::translate('Admin - Facturen', 'Bedrag incl', 'Bedrag incl.'),
                            ),
                            array(
                                'field' => 'DESCRIPTION_SHORT',
                                'title' => KJLocalization::translate('Admin - Facturen', 'Type factuur', 'Type factuur'),
                            ),
                        ),
                    )
                ) }}
            @endslot
        @endcomponent
    </div>
@endsection

@section('page-resources')
    {!! Html::script('/assets/custom/js/admin/finance/prepare/main.js?v='.Cache::get('cache_version_number')) !!}
@endsection