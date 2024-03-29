<?php

namespace App\Mail\Admin\Invoice;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use KJ\Core\mail\BaseMail;
use KJLocalization;

class Reminder extends BaseMail
{
    use Queueable;

    public $invoice;
    public $pdf;
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
        $this->pdf = Storage::disk('ftp')->get($this->invoice->document->FILEPATH);
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
            ->subject(KJLocalization::translate('Admin - Facturen', 'Herinnering factuur', 'Herinnering factuur', [], $this->locale) . ' ' . ($this->invoice->NUMBER ?? KJLocalization::translate('Admin - Facturen', 'Concept', 'Concept', [], $this->locale)))
            ->embed('logo', public_path('/assets/custom/img/logos/mail/logo.png'))
            ->embed('twitter', public_path('/assets/custom/img/logos/mail/twitter.png'))
            ->embed('facebook', public_path('/assets/custom/img/logos/mail/facebook.png'))
            ->embed('linkedin', public_path('/assets/custom/img/logos/mail/instagram.png'))
            ->attachData($this->pdf, KJLocalization::translate('Admin - Facturen', 'Factuur', 'Factuur', [], $this->locale) . ' ' . ($this->invoice->NUMBER ?? KJLocalization::translate('Admin - Facturen', 'Concept', 'Concept', [], $this->locale)) . '.pdf', [
                'mime' => 'application/pdf',
            ])
            ->markdown('admin.mails.invoice.reminder');

        return $email;
    }
}