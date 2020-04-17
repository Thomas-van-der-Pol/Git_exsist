@component('mail::message', [
'title' => KJLocalization::translate('Admin - Facturen', 'Factuur', 'Factuur', [], $locale) . ' ' . ($invoice->NUMBER ?? KJLocalization::translate('Admin - Facturen', 'Concept', 'Concept', [], $locale)),
'logo' => $logo,
'language' => $locale
])

{!! KJLocalization::translate('E-mails', 'Email herinnering tekst', 'Beste,<br/>
<br/>
Volgens onze administratie is de volgende factuur nog niet voldaan:<br/>
Factuurnummer: :NUMBER<br/>
Openstaand bedrag: :AMOUNTINCL<br/>
Factuurdatum: :DATE<br/>
Vervaldatum: :DUEDATE<br/>
Aantal dagen openstaand: :DAYSOLD<br/>
<br/>
Wij verzoeken u vriendelijk om het verschuldigde bedrag alsnog per omgaande te voldoen.<br/>
<br/>
Wij rekenen op uw spoedige betaling. Mocht uw betaling 7 dagen na deze herinnering niet zijn ontvangen, dan zijn wij genoodzaakt om de werkzaamheden op te schorten totdat uw betaling ontvangen is.<br/>
<br/>
Mocht uw betaling reeds onderweg zijn, dan vragen wij u om ons daarvan op de hoogte te stellen.',
[
    'NUMBER'    => $invoice->NUMBER,
    'AMOUNTINCL'    => $invoice->TotalInclFormatted,
    'DATE'    => $invoice->DateFormatted,
    'DUEDATE'    => $invoice->ExpirationDateFormatted,
    'DAYSOLD'    => $invoice->DaysOpen
], $locale, true) !!}

@endcomponent