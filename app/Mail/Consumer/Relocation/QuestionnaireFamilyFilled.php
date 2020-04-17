<?php

namespace App\Mail\Consumer\Relocation;

use App\Libraries\Consumer\RelocationUtils;
use App\Models\Admin\CRM\Family;
use App\Models\Admin\Questionnaire\Questionnaire;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use KJ\Core\mail\BaseMail;
use KJLocalization;

class QuestionnaireFamilyFilled extends BaseMail
{
    use Queueable, SerializesModels;

    public $family;
    public $relocation;
    public $questionnaire;
    public $language;

    /**
     * Create a new message instance.
     *
     * @param Family $family
     */
    public function __construct(Family $family)
    {
        $this->family = $family;
        $this->relocation = RelocationUtils::getActiveRelocation($family->ID);

        $this->questionnaire = Questionnaire::where([
            'ACTIVE' => true,
            'DEFAULT' => true
        ])->first();

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
            ->markdown('consumer.mails.questionnaire-family-filled');

        return $email;
    }
}