@component('mail::message', [
'title' => KJLocalization::translate('E-mails', 'Change your password', 'Change your password', [], $language),
'logo' => $logo,
'language' => $language
])
# {{ KJLocalization::translate('E-mails', 'Dear', 'Dear', [], $language) }} {{ $name }},
{{ KJLocalization::translate('E-mails', 'Forgot password text', 'You forgot your password, no problem. Create a new password with the button below.', [], $language) }}

@component('mail::panel')
    @component('mail::button', ['url' => $url])
        {{ KJLocalization::translate('E-mails', 'New password', 'New password', [], $language) }}
    @endcomponent
@endcomponent
<br/>

# {{ KJLocalization::translate('E-mails', 'Title no new password requested', 'No new password requested?', [], $language) }}
{{ KJLocalization::translate('E-mails', 'Text no new password requested', 'No worries. Your password has not changed yet. You do not trust it? Please contact our customer service.', [], $language) }}

@slot('subcopy')
# {{ KJLocalization::translate('E-mails', 'Link does not work', 'Link does not work?', [], $language) }}
{{ KJLocalization::translate('E-mails', 'Text link does not work', 'Copy and paste the link below into the address bar of your internet browser.', [], $language) }}<br/>
[{{ $url}}]({{ $url}})
@endslot
@endcomponent