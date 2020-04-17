@extends('theme.demo1.main', ['title' => KJLocalization::translate('Admin - Menu', 'Facturen', 'Facturen')])

@section('subheader')
    @component('breadcrums::main', [
        'rootURL'       => \KJ\Localization\libraries\LanguageUtils::getUrl('admin'),
        'parentTitle' => KJLocalization::translate('Admin - Menu', 'Facturen', 'Facturen'),
        'items' => [
            [
                'title' => KJLocalization::translate('Algemeen', 'Home', 'Home'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin'), '/')
            ],
            [
                'title' => KJLocalization::translate('Admin - Menu', 'Facturen', 'Facturen'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/invoice'), '/')
            ]
        ]
    ])
    @endcomponent
@endsection

@section('content')
    <div class="row">
        @component('portlet::main', ['notitle' => true, 'colsize' => 2])
            @php($currentTab = \KJ\Core\libraries\SessionUtils::getSession('ADM_INVOICE', 'CURRENT_TAB', config('invoice_state_type.TYPE_CONCEPT')))

            <div class="kt-widget kt-widget--user-profile-1 pb-0">
                <div class="kt-widget__body">
                    <div class="kt-widget__items nav" role="tablist">
                        <a href="#all_invoices" class="kt-widget__item {{ ($currentTab == config('invoice_state_type.TYPE_ALL')) ? 'kt-widget__item--active' : '' }}" data-toggle="tab" role="tab">
                            <span class="kt-widget__section">
                                <span class="kt-widget__desc">
                                    {{ KJLocalization::translate('Admin - Facturen', 'Alle', 'Alle') }}
                                </span>
                            </span>
                        </a>

                        <a href="#concept_invoices" class="kt-widget__item {{ ($currentTab == config('invoice_state_type.TYPE_CONCEPT')) ? 'kt-widget__item--active' : '' }}" data-toggle="tab" role="tab" aria-selected="false">
                            <span class="kt-widget__section">
                                <span class="kt-widget__desc">
                                    {{ KJLocalization::translate('Admin - Facturen', 'Concept', 'Concept') }}
                                </span>
                            </span>
                        </a>

                        <a href="#open_invoices" class="kt-widget__item {{ ($currentTab == config('invoice_state_type.TYPE_OPEN')) ? 'kt-widget__item--active' : '' }}" data-toggle="tab" role="tab" aria-selected="false">
                            <span class="kt-widget__section">
                                <span class="kt-widget__desc">
                                    {{ KJLocalization::translate('Admin - Facturen', 'Openstaand', 'Openstaand') }}
                                </span>
                            </span>
                        </a>

                        <a href="#expired_invoices" class="kt-widget__item {{ ($currentTab == config('invoice_state_type.TYPE_EXPIRED')) ? 'kt-widget__item--active' : '' }}" data-toggle="tab" role="tab" aria-selected="false">
                            <span class="kt-widget__section">
                                <span class="kt-widget__desc">
                                    {{ KJLocalization::translate('Admin - Facturen', 'Vervallen', 'Vervallen') }}
                                </span>
                            </span>
                        </a>

                        {{-- @TODO: TIJDELIJK UIT, BIJ BOEKHOUDKOPPELING AANZETTEN --}}
                        {{--<a href="#paid_invoices" class="kt-widget__item {{ ($currentTab == config('invoice_state_type.TYPE_PAID')) ? 'kt-widget__item--active' : '' }}" data-toggle="tab" role="tab" aria-selected="false">--}}
                            {{--<span class="kt-widget__section">--}}
                                {{--<span class="kt-widget__desc">--}}
                                    {{--{{ KJLocalization::translate('Admin - Facturen', 'Betaald', 'Betaald') }}--}}
                                {{--</span>--}}
                            {{--</span>--}}
                        {{--</a>--}}
                    </div>
                </div>
            </div>
        @endcomponent

        <div class="col-lg-10">
            <div class="kt-portlet kt-portlet--mobile" id="ASSIGNEE_FILTER">
                <div class="kt-portlet__body">
                    <div class="kt-form kt-form--label-right">
                        <div class="row align-items-center">
                            <div class="col-6 order-2 order-xl-1">
                                <div class="row align-items-center">

                                    <div class="col-auto">
                                        <div class="form-inline md-form filter-icon">
                                            {{ Form::text(
                                                'ADM_INVOICE_FILTER_SEARCH',
                                                \KJ\Core\libraries\SessionUtils::getSession('ADM_INVOICE', 'ADM_INVOICE_FILTER_SEARCH', ''),
                                                array(
                                                    'class'         => 'form-control filter hasSessionState',
                                                    'placeholder'   => KJLocalization::translate('Algemeen', 'Zoeken', 'Zoeken') . '..',
                                                    'id'            => 'ADM_INVOICE_FILTER_SEARCH',
                                                    'data-module'   => 'ADM_INVOICE',
                                                    'data-key'      => 'ADM_INVOICE_FILTER_SEARCH'
                                                )
                                            ) }}
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <div class="kt-form__group kt-form__group--inline">
                                            <div class="kt-form__label">
                                                {{ Form::label('ADM_FILTER_INVOICE_DATE', KJLocalization::translate('Admin - Facturen', 'Datum', 'Datum'). ':') }}
                                            </div>
                                            <div class="kt-form__control">
                                                <?php
                                                    $options = [];
                                                    if (\KJ\Core\libraries\SessionUtils::getSession('ADM_INVOICE', 'ADM_FILTER_INVOICE_DATE_startDate', '') != '') {
                                                        $options = [
                                                            'data-start-date' => \KJ\Core\libraries\SessionUtils::getSession('ADM_INVOICE', 'ADM_FILTER_INVOICE_DATE_startDate', ''),
                                                            'data-end-date' => \KJ\Core\libraries\SessionUtils::getSession('ADM_INVOICE', 'ADM_FILTER_INVOICE_DATE_endDate', ''),
                                                        ];
                                                    }
                                                ?>

                                                {{ KJField::daterangepicker(
                                                    'ADM_FILTER_INVOICE_DATE_GROUP',
                                                    'ADM_FILTER_INVOICE_DATE',
                                                    '',
                                                    array_merge(array(
                                                        'class' => 'form-control filter kjdaterangepicker-picker hasSessionState',
                                                        'data-module'   => 'ADM_INVOICE',
                                                        'data-key'      => 'ADM_FILTER_INVOICE_DATE',
                                                        'data-locale-format' => \KJ\Localization\libraries\LanguageUtils::getJSDateFormat()
                                                    ), $options)
                                                ) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 order-2 order-xl-2">
                                <a href="{{ \KJ\Localization\libraries\LanguageUtils::getUrl('admin/invoice/detail/-1') }}" id="newInvoice" class="btn btn-success btn-sm btn-upper ml-2 pull-right" style="display: none;">
                                    <i class="fa fa-plus-square"></i>
                                    {{ KJLocalization::translate('Admin - Facturen', 'Factuur', 'Factuur')}}
                                </a>

                                <a href="javascript:;" id="sendBulkInvoice" class="btn btn-brand btn-sm btn-upper ml-2 pull-right" style="display: none;">
                                    <i class="fa fa-paper-plane"></i>
                                    {{ KJLocalization::translate('Admin - Facturen', 'Selectie factureren', 'Selectie factureren')}}
                                </a>

                                <a href="javascript:;" id="sendBulkReminder" class="btn btn-warning btn-sm btn-upper ml-2 pull-right" style="display: none;">
                                    <i class="fa fa-bell"></i>
                                    {{ KJLocalization::translate('Admin - Facturen', 'Selectie herrinnering versturen', 'Selectie herrinnering versturen')}}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="kt-portlet kt-portlet--mobile" id="detailScreenContainer">
                <div class="kt-portlet__body kt-portlet__body--fit">
                    <div class="tab-content">
                        <div class="tab-pane {{ ($currentTab == config('invoice_state_type.TYPE_ALL')) ? 'active' : '' }}" id="all_invoices" data-id="-1" data-type="{{ config('invoice_state_type.TYPE_ALL') }}" role="tabpanel"></div>
                        <div class="tab-pane {{ ($currentTab == config('invoice_state_type.TYPE_CONCEPT')) ? 'active' : '' }}" id="concept_invoices" data-id="-1" data-type="{{ config('invoice_state_type.TYPE_CONCEPT') }}" role="tabpanel"></div>
                        <div class="tab-pane {{ ($currentTab == config('invoice_state_type.TYPE_OPEN')) ? 'active' : '' }}" id="open_invoices" data-id="-1" data-type="{{ config('invoice_state_type.TYPE_OPEN') }}" role="tabpanel"></div>
                        <div class="tab-pane {{ ($currentTab == config('invoice_state_type.TYPE_EXPIRED')) ? 'active' : '' }}" id="expired_invoices" data-id="-1" data-type="{{ config('invoice_state_type.TYPE_EXPIRED') }}" role="tabpanel"></div>
                        <div class="tab-pane {{ ($currentTab == config('invoice_state_type.TYPE_PAID')) ? 'active' : '' }}" id="paid_invoices" data-id="-1" data-type="{{ config('invoice_state_type.TYPE_PAID') }}" role="tabpanel"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('page-resources')
    {!! Html::script('/assets/custom/js/admin/finance/invoice/main.js?v='.Cache::get('cache_version_number')) !!}
@endsection