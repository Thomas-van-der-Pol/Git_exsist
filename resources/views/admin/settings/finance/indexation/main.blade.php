@extends('theme.demo1.main', ['title' => KJLocalization::translate('Admin - Menu', 'Indexatie', 'Indexatie')])

@section('subheader')
    @component('breadcrums::main', [
        'rootURL'       => \KJ\Localization\libraries\LanguageUtils::getUrl('admin'),
        'parentTitle' => KJLocalization::translate('Admin - Menu', 'Indexatie', 'Indexatie'),
        'items' => [
            [
                'title' => KJLocalization::translate('Algemeen', 'Home', 'Home'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin'), '/')
            ],
            [
                'title' => KJLocalization::translate('Admin - Menu', 'Instellingen', 'Instellingen'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings'), '/')
            ],
            [
                'title' => KJLocalization::translate('Admin - Menu', 'Financieel', 'Financieel'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/finance'), '/')
            ],
            [
                'title' => KJLocalization::translate('Admin - Menu', 'Indexatie', 'Indexatie'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/finance/indexation/configure'), '/')
            ]
        ]
    ])
    @endcomponent
@endsection

@section('content')
    <div class="col-lg-12">
        <div class="kt-portlet kt-portlet--mobile ">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <span class="kt-portlet__head-icon">
                        <a href="javascript:history.back();"><i class="la la-arrow-left"></i></a>
                    </span>

                    <h3 class="kt-portlet__head-title">
                        {{ KJLocalization::translate('Admin - Financieel', 'Indexatie', 'Indexatie') }}
                    </h3>
                </div>

                <div class="kt-portlet__head-toolbar">

                </div>
            </div>

            <div class="kt-portlet__body kt-portlet__body">
                <div id="items"></div>
            </div>
            <div class="kt-portlet__footer pb-3">
                <a href="javascript:;" class="btn btn-brand btn-md pull-right indexFinance mr-3" data-update="1">
                    <i class="m-menu__link-icon flaticon-diagram"></i>
                    {{ KJLocalization::translate('Admin - Financieel', 'Indexatie doorvoeren', 'Indexatie doorvoeren') }}
                </a>
            </div>
        </div>
    </div>
@endsection

@section('page-resources')
    {!! Html::script('/assets/custom/js/admin/setting/finance/indexation/main.js?v='.Cache::get('cache_version_number')) !!}
@endsection