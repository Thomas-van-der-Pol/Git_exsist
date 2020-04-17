@component('mail::message', [
'title' => KJLocalization::translate('E-mails', 'Nieuw wachtwoord', 'Nieuw wachtwoord'),
'logo' => $logo
])

{{ KJLocalization::translate('E-mails', 'Beste', 'Beste') }} {{ $contact->FULLNAME }},<br/>
{!! KJLocalization::translate('E-mails', 'Nieuw documenten wachtwoord tekst', 'Er is zojuist een nieuw documenten wachtwoord voor je aangemaakt:') !!}
@component('mail::panel')
{!! KJLocalization::translate('E-mails', 'Uw inloggevens', 'Uw inloggevens') !!} <br/>
{!! KJLocalization::translate('E-mails', 'E-mailadres', 'E-mailadres') !!}: {{ $contact->EMAILADDRESS }} <br/>
{!! KJLocalization::translate('E-mails', 'Wachtwoord', 'Wachtwoord') !!}: {{ $pw }}
@endcomponent

@endcomponent