<?php

namespace App\Mail\Admin\Project;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\App;
use KJ\Core\mail\BaseMail;
use KJLocalization;

class ShareDocuments extends BaseMail
{
    use Queueable;

    public $collection;
    public $url;
    public $locale;

    /**
     * Create a notification instance.
     *
     * @param $collection
     */
    public function __construct($collection)
    {
        $this->collection = $collection;
        $this->url = url(route('document.share', ['GUID' => $this->collection->GUID]));

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
            ->subject(KJLocalization::translate('Admin - Dossiers', 'Er zijn documenten met u gedeeld', 'Er zijn documenten met u gedeeld', [], $this->locale))
            ->embed('logo', public_path('/assets/custom/img/logos/mail/logo.png'))
            ->markdown('admin.mails.share-documents');

        return $email;
    }
}