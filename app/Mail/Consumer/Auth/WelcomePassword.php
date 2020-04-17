<?php

namespace App\Mail\Consumer\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
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

        $this->language = 'NL';//STATIC //$notifiable->getLocale();
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
            ->markdown('consumer.auth.passwords.welcome', [
                'url' => url(route('password.reset') . '/' . $this->token),
                'name' => $this->notifiable->title,
                'email' => $this->notifiable->getEmailForPasswordReset(),
                'language' => $this->language
            ]);

        return $email;
    }
}