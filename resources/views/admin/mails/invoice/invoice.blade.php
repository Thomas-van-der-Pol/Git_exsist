@component('mail::message', [
'title' => KJLocalization::translate('Admin - Facturen', 'Factuur', 'Factuur', [], $locale) . ' ' . ($invoice->NUMBER ?? KJLocalization::translate('Admin - Facturen', 'Concept', 'Concept', [], $locale)),
'logo' => $logo,
'language' => $locale
])

{!! KJLocalization::translate('E-mails', 'Email factuur tekst', 'Beste,<br/><br/>In de bijlage is uw factuur met nummer :NUMBER bijgesloten.', [
    'NUMBER' => $invoice->NUMBER
], $locale, true) !!}

@endcomponent