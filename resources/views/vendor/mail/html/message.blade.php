@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', [
'url' => config('app.url'),
'title' => $title
])
<img align="center" src="{{ $logo }}" alt="Logo" title="Logo" height="50"/>
@endcomponent
@endslot

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
<span>{{ KJLocalization::translate('E-mails', 'Telefoonnummer', 'Telefoonnummer', [], ($language ?? (config('language.defaultLang') ? config('language.defaultLang') : App::getLocale()))) }}: </span>{{ config('mail.contact_details.phone') }}<br/>
<span>{{ KJLocalization::translate('E-mails', 'Email', 'Email', [], ($language ?? (config('language.defaultLang') ? config('language.defaultLang') : App::getLocale()))) }}: </span><a href="mailto:{{ config('mail.contact_details.address') }}" target="_blank" rel="noopener">{{ config('mail.contact_details.address') }}</a>
@endcomponent
@endslot
@endcomponent
