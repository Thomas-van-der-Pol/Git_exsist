@extends('theme.demo1.main', ['title' => KJLocalization::translate('Admin - Menu', 'Profiel', 'Profiel')])

@section('subheader')
    @component('breadcrums::main', [
        'rootURL'       => \KJ\Localization\libraries\LanguageUtils::getUrl('admin'),
        'parentTitle'   => KJLocalization::translate('Admin - Menu', 'Profiel', 'Profiel'),
        'items' => [
            [
                'title' => KJLocalization::translate('Algemeen', 'Home', 'Home'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin'), '/')
            ],
            [
                'title' => KJLocalization::translate('Admin - Menu', 'Profiel', 'Profiel'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/profile'), '/')
            ]
        ]
    ])
    @endcomponent
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="alert alert-light alert-elevate" role="alert">
                <div class="alert-icon alert-icon-top">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon kt-svg-icon--big">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24"/>
                            <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10"/>
                            <path d="M12,16 C12.5522847,16 13,16.4477153 13,17 C13,17.5522847 12.5522847,18 12,18 C11.4477153,18 11,17.5522847 11,17 C11,16.4477153 11.4477153,16 12,16 Z M10.591,14.868 L10.591,13.209 L11.851,13.209 C13.447,13.209 14.602,11.991 14.602,10.395 C14.602,8.799 13.447,7.581 11.851,7.581 C10.234,7.581 9.121,8.799 9.121,10.395 L7.336,10.395 C7.336,7.875 9.31,5.922 11.851,5.922 C14.392,5.922 16.387,7.875 16.387,10.395 C16.387,12.915 14.392,14.868 11.851,14.868 L10.591,14.868 Z" fill="#000000"/>
                        </g>
                    </svg>
                </div>
                <div class="alert-text">
                    {!! KJLocalization::translate('Admin - Profiel', 'Introductietekst wachtwoord wijzigen', 'Wijzig hieronder uw wachtwoord. U kunt uw nieuwe wachtwoord direct gebruiken nadat u het succesvol hebt gewijzigd.') !!}
                </div>
            </div>
        </div>

        @component('portlet::main', ['colsize' => 12, 'notitle' => true])
            {{ Form::open(array(
                'method' => 'post',
                'id' => 'detailForm',
                'class' => 'kt-form',
                'novalidate'
            )) }}

            {{ Form::hidden('ID', $item ? $item->ID : -1) }}

                {{ KJField::password('USER_PASSWORD', KJLocalization::translate('Admin - Profiel', 'Huidig wachtwoord', 'Huidig wachtwoord'), ['required']) }}
                {{ KJField::password('USER_PASSWORD_NEW', KJLocalization::translate('Admin - Profiel', 'Nieuwe wachtwoord', 'Nieuwe wachtwoord'), ['required', 'minlength' => 8]) }}
                {{ KJField::password('USER_PASSWORD_NEW_CONFIRM', KJLocalization::translate('Admin - Profiel', 'Bevestig nieuwe wachtwoord', 'Bevestig nieuwe wachtwoord'), ['required', 'minlength' => 8]) }}

                {{ KJField::saveCancel('btnSave', 'btnCancel', false, [
                    'removeCancel' => true,
                    'saveText' => KJLocalization::translate('Algemeen', 'Opslaan', 'Opslaan')
                ]) }}

            {{ Form::close() }}
        @endcomponent
    </div>
@endsection

@section('page-resources')
    {!! Html::script('assets/custom/js/admin/profile/main.js?v='.Cache::get('cache_version_number')) !!}
@endsection