<?php

namespace App\Notifications\Consumer\Auth;

use Illuminate\Bus\Queueable;
use App\Mail\Consumer\Auth\ResetPassword as Mailable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use KJ\Localization\LocalizationFacade as KJLocalization;

class ResetPassword extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;
    public $language;

    /**
     * Create a notification instance.
     *
     * @param  string $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param $notifiable
     * @return Mailable
     */
    public function toMail($notifiable)
    {
        $this->language = $notifiable->getLocale();

        return (
            new Mailable($this->token, $notifiable))
            ->subject(KJLocalization::translate('E-mails', 'Change your password', 'Change your password', [], $this->language))
            ->to($notifiable->routeNotificationForMail());
    }
}
