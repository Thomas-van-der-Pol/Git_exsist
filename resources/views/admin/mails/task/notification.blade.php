@component('mail::message', [
'title' => KJLocalization::translate('E-mails', 'Taak toegewezen', 'Taak toegewezen', [], $locale),
'logo' => $logo,
'twitter' => $twitter,
'facebook' => $facebook,
'linkedin' => $linkedin,
'language' => $locale
])

{{ KJLocalization::translate('E-mails', 'Beste', 'Beste', [], $locale) }} {{ ( $item->assignee->FULLNAME ?? '' ) }},<br/>
{!! KJLocalization::translate('E-mails', 'Nieuwe taak toegewezen tekst', 'Er is zojuist een taak aan je toegewezen:', [], $locale) !!}
@component('mail::panel')
{{ KJLocalization::translate('E-mails', 'Onderwerp', 'Onderwerp', [], $locale) }}: {!! ( $item->SUBJECT ?? '' )!!}<br/>
{{ KJLocalization::translate('E-mails', 'Inhoud taak', 'Inhoud taak', [], $locale) }}: {!! ( $item->CONTENT ?? '' )!!}<br/><br/>
@component('mail::button', ['url' => $url])
    {{ KJLocalization::translate('E-mails', 'Open taak', 'Open taak', [], $locale) }}
@endcomponent
@endcomponent
<br/>

@slot('subcopy')
# {{ KJLocalization::translate('E-mails', 'Werkt de link niet?', 'Werkt de link niet?', [], $locale) }}
{{ KJLocalization::translate('E-mails', 'Werkt de link niet text uitleg', 'Kopieer en plak onderstaande link in het adresvak van je browser.', [], $locale) }}<br/>
[{{ $url}}]({{ $url}})
@endslot

{!! KJLocalization::translate('E-mails', 'Met vriendelijke groet,', 'Met vriendelijke groet,', [], $locale, true) !!}<br/>
{{ config('mail.contact_details.name') }}<br/>
{{ config('mail.contact_details.mail_address') }}<br/>
{{ config('mail.contact_details.website') }}<br/>

<img align="center" src="{{ $logo }}" alt="Logo" title="Logo" height="200"/>

@endcomponent