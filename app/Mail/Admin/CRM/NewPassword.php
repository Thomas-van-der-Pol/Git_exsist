<?php

namespace App\Mail\Admin\CRM;

use Illuminate\Bus\Queueable;
use KJ\Core\mail\BaseMail;
use KJLocalization;

class NewPassword extends BaseMail
{
    use Queueable;

    public $contact;
    public $pw;

    /**
     * Create a notification instance.
     *
     * @param  string $token
     */
    public function __construct($contact, $pw)
    {
        $this->contact = $contact;
        $this->pw = $pw;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->from(config('mail.from.address'), config('mail.from.name'))
            ->subject(KJLocalization::translate('E-mails', 'Nieuw wachtwoord', 'Nieuw wachtwoord'))
            ->embed('logo', public_path('/assets/custom/img/logos/mail/logo.png'))
            ->markdown('admin.mails.new-password', [
                'contact' => $this->contact,
                'pw' => $this->pw
            ]);

        return $email;
    }
}