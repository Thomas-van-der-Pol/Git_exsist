@extends('theme.demo1.main', ['title' => $item ? $item->title : KJLocalization::translate('Admin - Producten & diensten', 'Nieuw product of dienst', 'Nieuw product of dienst')])

@section('subheader')
    @component('breadcrums::main', [
        'rootURL'       => \KJ\Localization\libraries\LanguageUtils::getUrl('admin'),
        'parentTitle' => KJLocalization::translate('Admin - Menu', 'Producten & diensten', 'Producten & diensten'),
        'items' => [
            [
                'title' => KJLocalization::translate('Algemeen', 'Home', 'Home'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin'), '/')
            ],
            [
                'title' => KJLocalization::translate('Admin - Menu', 'Producten & diensten', 'Producten & diensten'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/product'), '/')
            ],
            [
                'title' => $item ? $item->title : KJLocalization::translate('Admin - Producten & diensten', 'Nieuw product of dienst', 'Nieuw product of dienst'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/product/detail/' . ( $item ? $item->ID : -1 )), '/')
            ]
        ]
    ])
    @endcomponent
@endsection

@section('content')
    <div class="row">
        @component('portlet::main', ['notitle' => true, 'colsize' => 12, 'portletClass' => 'kt-portlet--height-fluid'])
            <div class="kt-widget kt-widget--user-profile-3" id="default" data-id="{{ ( $item ? $item->ID : -1 ) }}">

            </div>
        @endcomponent
    </div>
@endsection

@section('page-resources')
    {!! Html::script('/assets/custom/js/admin/assortment/product/detail.js?v='.Cache::get('cache_version_number')) !!}
@endsection