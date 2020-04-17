<?php

namespace App\Notifications\Admin\Auth;

use Illuminate\Bus\Queueable;
use App\Mail\Admin\Auth\WelcomePassword as Mailable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use KJ\Localization\LocalizationFacade as KJLocalization;

class WelcomePassword extends Notification implements ShouldQueue
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
        $this->language = strtolower(config('language.defaultLang'));
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
        return (
            new Mailable($this->token, $notifiable))
            ->subject(KJLocalization::translate('E-mails', 'Welkom bij Emma Handson', 'Welkom bij Emma Handson', [], $this->language))
            ->to($notifiable->routeNotificationForMail());
    }
}
