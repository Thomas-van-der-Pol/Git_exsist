<?php

namespace App\Mail\Consumer\Relocation;

use App\Models\Admin\CRM\Family;
use App\Models\Admin\CRM\Relocation;
use App\Models\Admin\User;
use App\Models\Core\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use KJ\Core\mail\BaseMail;
use KJLocalization;

class DocumentUploaded extends BaseMail
{
    use Queueable, SerializesModels;

    public $document;
    public $user;
    public $relocation;
    public $family;
    public $language;

    /**
     * Create a new message instance.
     *
     * @param Document $document
     * @param User $user
     * @param Family $family
     */
    public function __construct(Document $document, User $user, Relocation $relocation)
    {
        $this->document = $document;
        $this->user = $user;
        $this->relocation = $relocation;
        $this->family = $relocation->family;

        $this->language = strtolower(config('language.defaultLang'));
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->from(config('mail.from.address'), config('mail.from.name'))
            ->subject(KJLocalization::translate('E-mails', 'Document uploaded', 'Document uploaded', [], $this->language))
            ->embed('logo', public_path('/assets/custom/img/logos/mail/logo.png'))
            ->markdown('consumer.mails.document-uploaded');

        return $email;
    }
}