<?php

namespace App\Mail\Admin\Invoice;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use KJ\Core\mail\BaseMail;
use KJLocalization;

class NewInvoiceCompensation extends BaseMail
{
    use Queueable;

    public $invoice;
    public $pdf_compensation_letter;
    public $pdf_compensation_invoice;
    public $locale;

    /**
     * Create a notification instance.
     *
     * @param $item
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function __construct($invoice)
    {
        $this->invoice = $invoice;
        $this->pdf_compensation_letter = Storage::disk('ftp')->get($this->invoice->document_compensation_letter->FILEPATH);
        $this->pdf_compensation_invoice = Storage::disk('ftp')->get($this->invoice->document_anonymized->FILEPATH);
        $this->locale = App::getLocale();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->from(config('mail.from.address'), config('mail.from.name'))
            ->subject(KJLocalization::translate('Admin - Facturen', 'Financiele bijdrage interventie', 'Financiële bijdrage interventie', [], $this->locale) . ' #' . ($this->invoice->NUMBER ?? KJLocalization::translate('Admin - Facturen', 'Concept', 'Concept', [], $this->locale)))
            ->embed('logo', public_path('/assets/custom/img/logos/mail/logo.png'))
            ->attachData($this->pdf_compensation_letter, KJLocalization::translate('Admin - Facturen', 'Vergoedingsbrief', 'Vergoedingsbrief', [], $this->locale) . ' ' . ($this->invoice->NUMBER ?? KJLocalization::translate('Admin - Facturen', 'Concept', 'Concept', [], $this->locale)) . '.pdf', [
                'mime' => 'application/pdf',
            ])
            ->attachData($this->pdf_compensation_invoice, KJLocalization::translate('Admin - Facturen', 'Geanonimiseerde kopie factuur', 'Geanonimiseerde kopie factuur', [], $this->locale) . ' ' . ($this->invoice->NUMBER ?? KJLocalization::translate('Admin - Facturen', 'Concept', 'Concept', [], $this->locale)) . '.pdf', [
                'mime' => 'application/pdf',
            ])
            ->markdown('admin.mails.invoice.compensation');

        return $email;
    }
}