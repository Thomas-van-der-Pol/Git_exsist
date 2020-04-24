@extends('theme.demo1.main', ['title' => KJLocalization::translate('Admin - Menu', 'Boekhouding', 'Boekhouding')])

@section('subheader')
    @component('breadcrums::main', [
        'rootURL'       => \KJ\Localization\libraries\LanguageUtils::getUrl('admin'),
        'parentTitle' => KJLocalization::translate('Admin - Menu', 'Boekhouding', 'Boekhouding'),
        'items' => [
            [
                'title' => KJLocalization::translate('Algemeen', 'Home', 'Home'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin'), '/')
            ],
            [
                'title' => KJLocalization::translate('Admin - Menu', 'Boekhouding', 'Boekhouding'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/accountancy'), '/')
            ]
        ]
    ])
    @endcomponent
@endsection

@section('content')
    <div class="row">
        @component('portlet::main', ['notitle' => true, 'colsize' => 2])
            @php($currentTab = \KJ\Core\libraries\SessionUtils::getSession('ADM_ACCOUNTANCY', 'CURRENT_TAB', config('accountancy_state_type.TYPE_DEBTOR')))

            <div class="kt-widget kt-widget--user-profile-1 pb-0">
                <div class="kt-widget__body">
                    <div class="kt-widget__items nav" role="tablist">
                        <a href="#debtors" class="kt-widget__item {{ ($currentTab == config('accountancy_state_type.TYPE_DEBTOR')) ? 'kt-widget__item--active' : '' }}" data-toggle="tab" role="tab">
                            <span class="kt-widget__section">
                                <span class="kt-widget__desc">
                                    {{ KJLocalization::translate('Admin - Boekhouding', 'Debiteuren', 'Debiteuren') }}
                                </span>
                            </span>
                        </a>

                        <a href="#creditors" class="kt-widget__item {{ ($currentTab == config('accountancy_state_type.TYPE_CREDITOR')) ? 'kt-widget__item--active' : '' }}" data-toggle="tab" role="tab" aria-selected="false">
                            <span class="kt-widget__section">
                                <span class="kt-widget__desc">
                                    {{ KJLocalization::translate('Admin - Boekhouding', 'Crediteuren', 'Crediteuren') }}
                                </span>
                            </span>
                        </a>

                        <a href="#invoices" class="kt-widget__item {{ ($currentTab == config('accountancy_state_type.TYPE_INVOICE')) ? 'kt-widget__item--active' : '' }}" data-toggle="tab" role="tab" aria-selected="false">
                            <span class="kt-widget__section">
                                <span class="kt-widget__desc">
                                    {{ KJLocalization::translate('Admin - Boekhouding', 'Financiele verkoop mutaties', 'Financiele verkoop mutaties') }}
                                </span>
                            </span>
                        </a>

                        <a href="#receivables" class="kt-widget__item {{ ($currentTab == config('accountancy_state_type.TYPE_RECEIVABLE')) ? 'kt-widget__item--active' : '' }}" data-toggle="tab" role="tab" aria-selected="false">
                            <span class="kt-widget__section">
                                <span class="kt-widget__desc">
                                    {{ KJLocalization::translate('Admin - Boekhouding', 'Openstaande posten', 'Openstaande posten') }}
                                </span>
                            </span>
                        </a>
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
                                                'ADM_ACCOUNTANCY_FILTER_SEARCH',
                                                \KJ\Core\libraries\SessionUtils::getSession('ADM_ACCOUNTANCY', 'ADM_ACCOUNTANCY_FILTER_SEARCH', ''),
                                                array(
                                                    'class'         => 'form-control filter hasSessionState',
                                                    'placeholder'   => KJLocalization::translate('Algemeen', 'Zoeken', 'Zoeken') . '..',
                                                    'id'            => 'ADM_ACCOUNTANCY_FILTER_SEARCH',
                                                    'data-module'   => 'ADM_ACCOUNTANCY',
                                                    'data-key'      => 'ADM_ACCOUNTANCY_FILTER_SEARCH'
                                                )
                                            ) }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 order-2 order-xl-2">
                                <a href="javascript:;" id="export" class="btn btn-brand btn-sm btn-upper ml-2 pull-right" style="display: none;">
                                    <i class="fa fa-paper-plane"></i>
                                    {{ KJLocalization::translate('Admin - Boekhouding', 'Exporteren', 'Exporteren')}}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="kt-portlet kt-portlet--mobile" id="detailScreenContainer">
                <div class="kt-portlet__body kt-portlet__body--fit">
                    <div class="tab-content">
                        <div class="tab-pane {{ ($currentTab == config('accountancy_state_type.TYPE_DEBTOR')) ? 'active' : '' }}" id="debtors" data-id="-1" data-type="{{ config('accountancy_state_type.TYPE_DEBTOR') }}" role="tabpanel"></div>
                        <div class="tab-pane {{ ($currentTab == config('accountancy_state_type.TYPE_CREDITOR')) ? 'active' : '' }}" id="creditors" data-id="-1" data-type="{{ config('accountancy_state_type.TYPE_CREDITOR') }}" role="tabpanel"></div>
                        <div class="tab-pane {{ ($currentTab == config('accountancy_state_type.TYPE_INVOICE')) ? 'active' : '' }}" id="invoices" data-id="-1" data-type="{{ config('accountancy_state_type.TYPE_INVOICE') }}" role="tabpanel"></div>
                        <div class="tab-pane {{ ($currentTab == config('accountancy_state_type.TYPE_RECEIVABLE')) ? 'active' : '' }}" id="receivables" data-id="-1" data-type="{{ config('accountancy_state_type.TYPE_RECEIVABLE') }}" role="tabpanel"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-resources')
    {!! Html::script('/assets/custom/js/admin/finance/accountancy/main.js?v='.Cache::get('cache_version_number')) !!}
@endsection