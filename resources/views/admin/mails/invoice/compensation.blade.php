@component('mail::message', [
'title' => KJLocalization::translate('Admin - Facturen', 'Financiele bijdrage interventie', 'Financiële bijdrage interventie', [], $locale) . ' #' . ($invoice->NUMBER ?? KJLocalization::translate('Admin - Facturen', 'Concept', 'Concept', [], $locale)),
'logo' => $logo,
'language' => $locale
])

{!! KJLocalization::translate('E-mails', 'Email factuur vergoeding tekst', 'Geachte heer / mevrouw,<br/><br/>Wij kunnen u meedelen dat er voor de in te zetten interventie een financiële bijdrage is toegezegd. In de bijlage treft u de vergoedingsbrief en geanonimiseerde kopie factuur.', [
    'NUMBER' => $invoice->NUMBER
], $locale, true) !!}

@endcomponent