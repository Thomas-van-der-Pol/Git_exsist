@extends('theme.demo5.main', ['title' => KJLocalization::translate('Portal - Menu', 'Profile', 'Profile')])

@section('subheader')
    @component('breadcrums::main', [
        'rootURL'       => '/',
        'parentTitle'   => KJLocalization::translate('Portal - Menu', 'Profile', 'Profile'),
        'items' => [
            [
                'title' => KJLocalization::translate('Algemeen', 'Home', 'Home'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('home'), '/')
            ],
            [
                'title' => KJLocalization::translate('Portal - Menu', 'Profile', 'Profile'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('profile'), '/')
            ]
        ]
    ])
    @endcomponent
@endsection

@section('content')
    <div class="alert alert-light alert-elevate" role="alert">
        <div class="alert-icon alert-icon-top"><i class="flaticon-info kt-font-brand"></i></div>
        <div class="alert-text">
            {!! KJLocalization::translate('Portal - My profile', 'Introduction text change password', 'Change your password below. You can use your new password directly after successfully changing it.') !!}
        </div>
    </div>

    @component('portlet::main', ['colsize' => '12 p-0', 'notitle' => true])
        {{ Form::open(array(
            'method' => 'post',
            'id' => 'detailForm',
            'class' => 'kt-form',
            'novalidate'
        )) }}

        {{ Form::hidden('ID', $item ? $item->ID : -1) }}

        {{ KJField::password('USER_PASSWORD', KJLocalization::translate('Portal - Profile', 'Current password', 'Current password'), ['required']) }}
        {{ KJField::password('USER_PASSWORD_NEW', KJLocalization::translate('Portal - Profile', 'New password', 'New password'), ['required', 'minlength' => 8]) }}
        {{ KJField::password('USER_PASSWORD_NEW_CONFIRM', KJLocalization::translate('Portal - Profile', 'Confirm new password', 'Confirm new password'), ['required', 'minlength' => 8]) }}

        {{ KJField::saveCancel('btnSave', 'btnCancel', false, [
            'removeCancel' => true,
            'saveText' => KJLocalization::translate('Algemeen', 'Opslaan', 'Opslaan')
        ]) }}

        {{ Form::close() }}
    @endcomponent

@endsection

@section('page-resources')
    {!! Html::script('assets/custom/js/consumer/profile/main.js?v='.Cache::get('cache_version_number')) !!}
@endsection