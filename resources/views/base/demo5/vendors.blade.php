{{-- Material Design --}}
{!! Html::script('/assets/custom/js/material_design/mdb.min.js?v='.Cache::get('cache_version_number')) !!}

{{-- Base KJ scripts --}}
{!! HTML::script('/assets/kj/localization/lang-' . App::getLocale() . '.js?v='.Cache::get('cache_version_number')) !!}
{!! HTML::script('/assets/kj/core/core-routes.js?v='.Cache::get('cache_version_number')) !!}
{!! HTML::script('/assets/kj/core/core.js?v='.Cache::get('cache_version_number')) !!}
{!! HTML::script('/assets/kj/datatable/datatable.js?v='.Cache::get('cache_version_number')) !!}
{!! HTML::script('/assets/kj/moreless/moreless.js?v='.Cache::get('cache_version_number')) !!}
{!! HTML::script('/assets/kj/field/field.js?v='.Cache::get('cache_version_number')) !!}
{!! HTML::script('/assets/kj/loader/loader.js?v='.Cache::get('cache_version_number')) !!}
{!! HTML::script('/assets/kj/localization/localization.js?v='.Cache::get('cache_version_number')) !!}

{{-- Scripts default locale 'en'. Only include when locale is different! --}}
@if(App::getLocale() != 'en')
    {!! HTML::script('/assets/kj/localization/validation/messages_' . App::getLocale() . '.js?v='.Cache::get('cache_version_number')) !!}
    {!! HTML::script('/assets/kj/localization/datepicker/bootstrap-datepicker.' . App::getLocale() . '.js?v='.Cache::get('cache_version_number')) !!}
@endif

{{-- Main scripts --}}
{!! HTML::script('/assets/custom/js/main.js?v='.Cache::get('cache_version_number')) !!}

{{-- Modals --}}
@include('field::modal', ['title' => null, 'titleClose' => KJLocalization::translate('Algemeen', 'Sluiten', 'Sluiten')])
@include('loader::loader')