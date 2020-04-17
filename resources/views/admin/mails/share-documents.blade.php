@component('mail::message', [
'title' => KJLocalization::translate('Admin - Dossiers', 'Er zijn documenten met u gedeeld', 'Er zijn documenten met u gedeeld', [], $locale),
'logo' => $logo,
'language' => $locale
])

{!! KJLocalization::translate('E-mails', 'Email gedeelde documenten tekst', 'Beste,<br/><br/>Er zijn documenten voor project :PROJECT met u gedeeld. Druk op onderstaande knop om de documenten in te zien.', [
    'PROJECT' => $collection->project->DESCRIPTION
], $locale, true) !!}

@component('mail::panel')
    @component('mail::button', ['url' => $url])
        {{ KJLocalization::translate('E-mails', 'Download documenten', 'Download documenten', [], $locale) }}
    @endcomponent
@endcomponent
<br/>

{!! KJLocalization::translate('E-mails', 'Email gedeelde documenten verloopdata', 'Let op: uw documenten zijn beperkt beschikbaar. Na ontvangst van deze e-mail zijn de documenten nog 7 dagen beschikbaar. Wanneer het eerste document wordt gedownload zijn de documenten nog maar 24 uur beschikbaar.', [], $locale, true) !!}

@slot('subcopy')
# {{ KJLocalization::translate('E-mails', 'Werkt de link niet?', 'Werkt de link niet?', [], $locale) }}
{{ KJLocalization::translate('E-mails', 'Werkt de link niet text uitleg', 'Kopieer en plak onderstaande link in het adresvak van je browser.', [], $locale) }}<br/>
[{{ $url}}]({{ $url}})
@endslot

@endcomponent