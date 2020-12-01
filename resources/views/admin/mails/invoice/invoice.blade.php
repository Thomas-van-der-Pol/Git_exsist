@component('mail::message', [
'title' => KJLocalization::translate('Admin - Facturen', 'Factuur', 'Factuur', [], $locale) . ' ' . ($invoice->NUMBER ?? KJLocalization::translate('Admin - Facturen', 'Concept', 'Concept', [], $locale)),
'twitter' => $twitter,
'facebook' => $facebook,
'linkedin' => $linkedin,
'language' => $locale
])

{!! KJLocalization::translate('E-mails', 'Email factuur intro', 'Geachte :SALUTATION :LASTNAME,', [
    'SALUTATION' => $contact->salutation ? lcfirst($contact->salutation->value) : 'heer / mevrouw' ,
    'LASTNAME' => $contact->LASTNAME,
], $locale, true) !!}<br/><br/>

{!! KJLocalization::translate('E-mails', 'Email factuur ontvang factuur', 'Hierbij ontvangt u factuur met factuurnummer :NUMBER voor de uitgevoerde interventie.', [
     'NUMBER' => $invoice->NUMBER,
 ], $locale, true) !!}<br/>

{!! KJLocalization::translate('E-mails', 'Email factuur betaling ontvangst', 'Graag zien wij de betaling binnen 14 dagen tegemoet.', [
     'NUMBER' => $invoice->NUMBER,
 ], $locale, true) !!}<br/>

{!! KJLocalization::translate('E-mails', 'Email factuur vragen', 'Mocht u vragen hebben over de factuur of een betalingsregeling willen treffen. Laat het ons dan gerust weten.', [
 ], $locale, true) !!}<br/>

{!! KJLocalization::translate('E-mails', 'Email factuur outro', 'Wij hopen u hiermee voldoende te hebben geïnformeerd.', [
 ], $locale, true) !!}<br/>

{!! KJLocalization::translate('E-mails', 'Met vriendelijke groet,', 'Met vriendelijke groet,', [], $locale, true) !!}<br/>
{{ config('mail.contact_details.name') }}<br/>
{{ config('mail.contact_details.mail_address') }}<br/>
{{ config('mail.contact_details.website') }}<br/>

<img align="center" src="{{ $logo }}" alt="Logo" title="Logo" height="200"/>
@endcomponent