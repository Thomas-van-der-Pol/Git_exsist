@component('mail::message', [
'title' => KJLocalization::translate('E-mails', 'Welcome to Edufax', 'Welcome to Edufax', [], $language),
'logo' => $logo,
'language' => $language
])
# {{ KJLocalization::translate('E-mails', 'Dear', 'Dear', [], $language) }} {{ $name }},
{!! KJLocalization::translate('E-mails', 'Welcome mail introduction text', 'An account has just been created for you on the website:', [], $language) !!}<br/>
<a href="{{url('/')}}">{{url('/')}}</a>
<br/>
{!! KJLocalization::translate('E-mails', 'Welcome mail explanation', 'From now on you can log in with email address :EMAILADDRESS as soon as you have set a password. Click on the button below to set the password.', [
    'EMAILADDRESS' => $email
], $language) !!}

@component('mail::panel')
    @component('mail::button', ['url' => $url])
        {{ KJLocalization::translate('E-mails', 'New password', 'New password', [], $language) }}
    @endcomponent
@endcomponent
<br/>

@slot('subcopy')
# {{ KJLocalization::translate('E-mails', 'Link does not work', 'Link does not work?', [], $language) }}
{{ KJLocalization::translate('E-mails', 'Text link does not work', 'Copy and paste the link below into the address bar of your internet browser.', [], $language) }}<br/>
[{{ $url}}]({{ $url}})
@endslot
@endcomponent