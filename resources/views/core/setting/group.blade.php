@extends('theme.demo1.main', ['title' => KJLocalization::translate('Admin - Menu', $item->DESCRIPTION, $item->DESCRIPTION)])

@section('subheader')
    @component('breadcrums::main', [
        'rootURL'       => \KJ\Localization\libraries\LanguageUtils::getUrl('admin'),
        'parentTitle' => KJLocalization::translate('Admin - Menu', $item->DESCRIPTION, $item->DESCRIPTION),
        'items' => [
            [
                'title' => KJLocalization::translate('Algemeen', 'Home', 'Home'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin'), '/')
            ],
            [
                'title' => KJLocalization::translate('Admin - Menu', 'Instellingen', 'Instellingen'),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/group/'. $item->ID), '/')
            ],
            [
                'title' => KJLocalization::translate('Admin - Menu', $item->DESCRIPTION, $item->DESCRIPTION),
                'URL' => ltrim(\KJ\Localization\libraries\LanguageUtils::getUrl('admin/settings/group/' . $item->ID), '/')
            ]
        ]
    ])
    @endcomponent
@endsection

@section('content')
    <div class="row">
        @component('portlet::main', ['notitle' => true, 'colsize' => 12])
            {{ Form::open(array(
                'method' => 'post',
                'id' => 'detailFormSetting',
                'class' => 'kt-form',
                'novalidate'
            )) }}
            <div class="kt-portlet__body">
                <div class="row">
                    <div class="col-xl-4 col-lg-6">
                        @foreach($settings as $setting)
                            @php
                                $value = \App\Models\Core\Setting\SettingValue::getValue($setting->ID);

                                $options = ['name' => 'SETTING['.$setting->ID.']'];
                                if ($setting->REQUIRED) {
                                    $options = array_merge($options, ['required']);
                                }
                            @endphp

                            @switch($setting->FK_CORE_SETTING_TYPE)
                                @case(config('setting_type.TYPE_TEXT'))
                                    {{ KJField::text('SETTING_' . $setting->ID, KJLocalization::translate('Admin - Settings', $setting->DESCRIPTION, $setting->DESCRIPTION), $value, true, $options) }}
                                @break

                                @case(config('setting_type.TYPE_EMAIL'))
                                    {{ KJField::email('SETTING_' . $setting->ID, KJLocalization::translate('Admin - Settings', $setting->DESCRIPTION, $setting->DESCRIPTION), $value, $options) }}
                                @break

                                @case(config('setting_type.TYPE_TIME'))
                                    {{ KJField::time('SETTING_' . $setting->ID, KJLocalization::translate('Admin - Settings', $setting->DESCRIPTION, $setting->DESCRIPTION), $value, $options) }}
                                @break

                                @case(config('setting_type.TYPE_NUMBER'))
                                    {{ KJField::number('SETTING_' . $setting->ID, KJLocalization::translate('Admin - Settings', $setting->DESCRIPTION, $setting->DESCRIPTION), $value, true, $options) }}
                                @break
                            @endswitch
                        @endforeach
                    </div>
                </div>

                @if($settings->count() > 0)
                    {{ KJField::saveCancel(
                        'btnSaveSetting',
                        'btnCancelSetting',
                        true,
                        array(
                            'saveText' => KJLocalization::translate('Algemeen', 'Opslaan', 'Opslaan'),
                            'cancelText' => KJLocalization::translate('Algemeen', 'Annuleren', 'Annuleren')
                        )
                    ) }}
                @endif
            </div>
            {{ Form::close() }}
        @endcomponent
    </div>  
@endsection

@section('page-resources')
    {!! Html::script('assets/custom/js/core/setting/group.js?v='.Cache::get('cache_version_number')) !!}
@endsection