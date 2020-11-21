@component('mail::message', [
'title' => KJLocalization::translate('E-mails', 'Wijzig je wachtwoord', 'Wijzig je wachtwoord', [], $language),
'logo' => $logo,
'twitter' => $twitter,
'facebook' => $facebook,
'linkedin' => $linkedin,
'language' => $language
])
# {{ KJLocalization::translate('E-mails', 'Beste', 'Beste', [], $language) }} {{ $name }},
{{ KJLocalization::translate('E-mails', 'Wachtwoord wijzigen text', 'Ben je je wachtwoord vergeten, geen probleem. Druk op onderstaande knop en stel je wachtwoord opnieuw in.', [], $language) }}

@component('mail::panel')
    @component('mail::button', ['url' => $url])
        {{ KJLocalization::translate('E-mails', 'Nieuw wachtwoord', 'Nieuw wachtwoord', [], $language) }}
    @endcomponent
@endcomponent
<br/>

# {{ KJLocalization::translate('E-mails', 'Geen nieuw wachtwoord aangevraagd?', 'Geen nieuw wachtwoord aangevraagd?', [], $language) }}
{{ KJLocalization::translate('E-mails', 'Tekst geen nieuw wachtwoord aangevraagd', 'Geen probleem. Je wachtwoord is nog niet gewijzigd. Vertrouw je het niet? Neem dan even contact met ons op.', [], $language) }}

@slot('subcopy')
# {{ KJLocalization::translate('E-mails', 'Werkt de link niet?', 'Werkt de link niet?', [], $language) }}
{{ KJLocalization::translate('E-mails', 'Werkt de link niet text uitleg', 'Kopieer en plak onderstaande link in het adresvak van je browser.', [], $language) }}<br/>
[{{ substr($url,0,strlen($url) / 2) }}]({{ $url  }})
[{{ substr($url,strlen($url) / 2, strlen($url)) }}]({{ $url }})
@endslot

{!! KJLocalization::translate('E-mails', 'Met vriendelijke groet,', 'Met vriendelijke groet,', [], $language, true) !!}<br/>
{{ config('mail.contact_details.name') }}<br/>
{{ config('mail.contact_details.mail_address') }}<br/>
{{ config('mail.contact_details.website') }}<br/>

<img align="center" src="{{ $logo }}" alt="Logo" title="Logo" height="200"/>

@endcomponent