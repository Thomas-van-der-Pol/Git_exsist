<?php

namespace App\Mail\Consumer\Relocation;

use App\Models\Admin\CRM\RelocationContact;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use KJ\Core\mail\BaseMail;
use KJLocalization;

class QuestionnaireFilled extends BaseMail
{
    use Queueable, SerializesModels;

    public $relocationContact;
    public $language;

    /**
     * Create a new message instance.
     *
     * @param $relocationContact
     */
    public function __construct(RelocationContact $relocationContact)
    {
        $this->relocationContact = $relocationContact;

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
            ->subject(KJLocalization::translate('E-mails', 'Questionnaire filled', 'Questionnaire filled', [], $this->language))
            ->embed('logo', public_path('/assets/custom/img/logos/mail/logo.png'))
            ->markdown('consumer.mails.questionnaire-filled');

        return $email;
    }
}