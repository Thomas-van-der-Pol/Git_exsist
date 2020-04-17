@component('mail::message', [
'title' => KJLocalization::translate('E-mails', 'Taak toegewezen', 'Taak toegewezen'),
'logo' => $logo
])

{{ KJLocalization::translate('E-mails', 'Beste', 'Beste') }} {{ ( $item->assignee->FULLNAME ?? '' ) }},<br/>
{!! KJLocalization::translate('E-mails', 'Nieuwe taak toegewezen tekst', 'Er is zojuist een taak aan je toegewezen:') !!}
@component('mail::panel')
{{ KJLocalization::translate('E-mails', 'Onderwerp', 'Onderwerp') }}: {!! ( $item->SUBJECT ?? '' )!!}<br/>
{{ KJLocalization::translate('E-mails', 'Inhoud taak', 'Inhoud taak') }}: {!! ( $item->CONTENT ?? '' )!!}<br/><br/>
@component('mail::button', ['url' => $url])
    {{ KJLocalization::translate('E-mails', 'Open taak', 'Open taak') }}
@endcomponent
@endcomponent
<br/>

@slot('subcopy')
# {{ KJLocalization::translate('E-mails', 'Werkt de link niet?', 'Werkt de link niet?') }}
{{ KJLocalization::translate('E-mails', 'Werkt de link niet text uitleg', 'Kopieer en plak onderstaande link in het adresvak van je browser.') }}<br/>
[{{ $url}}]({{ $url}})
@endslot

@endcomponent