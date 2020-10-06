@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', [
'url' => config('app.url'),
'title' => $title
])

@endcomponent
@endslot

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')

@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
<table>
<tr>
<td style="width: 85%">
<h2>{{ KJLocalization::translate('E-mails', 'Adres', 'Adres', [], ($language ?? (config('language.defaultLang') ? config('language.defaultLang') : App::getLocale()))) }}</h2>
</td>
<td>
<h2>{{ KJLocalization::translate('E-mails', 'Volg ons', 'Volg ons', [], ($language ?? (config('language.defaultLang') ? config('language.defaultLang') : App::getLocale()))) }}</h2>
</td>
</tr>
<tr></tr>
<tr>
<td>
{{ config('app.name') }}
</td>
<td>
<a href="https://twitter.com/Exsist_tweet"><img align="center" src="{{ $twitter ?? '' }}" alt="Twitter" title="Twitter" height="20"></a>
</td>
</tr>
<tr>
<td>
{{ config('mail.contact_details.address') }}
</td>
<td>
<a href="https://www.facebook.com/exsist.leiderschap/"><img align="center" src="{{ $facebook ?? '' }}" alt="Facebook" title="Facebook" height="20"></a>
</td>
</tr>
<tr>
<td>
{{ config('mail.contact_details.zipcode').' '.config('mail.contact_details.city') }}
</td>
<td>
<a href="https://www.linkedin.com/in/henrichleenders/"><img align="center" src="{{ $linkedin ?? '' }}" alt="Instagram" title="Instagram" height="20"></a>
</td>
</tr>
<tr>
<td>
{{ config('mail.contact_details.country') }}
</td>
</tr>
<tr>
<td>
<span>{{ KJLocalization::translate('E-mails', 'T:', 'T:', [], ($language ?? (config('language.defaultLang') ? config('language.defaultLang') : App::getLocale()))) }}: </span>{{ config('mail.contact_details.phone') }}<br/>
</td>
</tr>
</table>
@endcomponent
@endslot
@endcomponent
