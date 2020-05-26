@component('mail::message', [
'title' => KJLocalization::translate('E-mails', 'Wijzig je wachtwoord', 'Wijzig je wachtwoord', [], $language),
'logo' => $logo,
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
@endcomponent