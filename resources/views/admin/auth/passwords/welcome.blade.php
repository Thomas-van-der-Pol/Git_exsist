@component('mail::message', [
'title' => KJLocalization::translate('E-mails', 'Welcome bij Emma Handson', 'Welcome to Emma Handson', [], $language),
'logo' => $logo,
'language' => $language
])
# {{ KJLocalization::translate('E-mails', 'Beste', 'Beste', [], $language) }} {{ $name }},
{!! KJLocalization::translate('E-mails', 'Welkomstmail text', 'Er is zojuist een account voor je aangemaakt op de website:', [], $language) !!}<br/>
<a href="{{url('/admin')}}">{{url('/admin')}}</a>
<br/>
{!! KJLocalization::translate('E-mails', 'Welkomstmail text uitleg', 'Vanaf heden kun je inloggen met het volgende e-mailadres :EMAILADDRESS zodra je een wachtwoord ingesteld hebt. Klik op onderstaande knop om een wachtwoord in te stellen.', [
    'EMAILADDRESS' => '<a href="mailto:'.$email.'">' . $email . '</a>'
], $language) !!}

@component('mail::panel')
    @component('mail::button', ['url' => $url])
        {{ KJLocalization::translate('E-mails', 'Nieuw wachtwoord', 'Nieuw wachtwoord', [], $language) }}
    @endcomponent
@endcomponent
<br/>

@slot('subcopy')
# {{ KJLocalization::translate('E-mails', 'Werkt de link niet?', 'Werkt de link niet?', [], $language) }}
{{ KJLocalization::translate('E-mails', 'Werkt de link niet text uitleg', 'Kopieer en plak onderstaande link in het adresvak van je browser.', [], $language) }}<br/>
[{{ $url}}]({{ $url}})
@endslot
@endcomponent