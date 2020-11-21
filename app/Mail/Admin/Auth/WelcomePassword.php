<?php

namespace App\Mail\Admin\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use KJ\Core\mail\BaseMail;

class WelcomePassword extends BaseMail
{
    use Queueable, SerializesModels;

    protected $token;
    protected $notifiable;
    protected $language;

    /**
     * Create a new message instance.
     *
     * @param $token
     * @param $notifiable
     */
    public function __construct($token, $notifiable)
    {
        $this->token        = $token;
        $this->notifiable   = $notifiable;

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
            ->embed('logo', public_path('/assets/custom/img/logos/mail/logo.png'))
            ->embed('twitter', public_path('/assets/custom/img/logos/mail/twitter.png'))
            ->embed('facebook', public_path('/assets/custom/img/logos/mail/facebook.png'))
            ->embed('linkedin', public_path('/assets/custom/img/logos/mail/instagram.png'))
            ->markdown('admin.auth.passwords.welcome', [
                'url' => url(route('admin.password.reset') . '/' . $this->token),
                'name' => $this->notifiable->title,
                'email' => $this->notifiable->getEmailForPasswordReset(),
                'language' => $this->language
            ]);

        return $email;
    }
}