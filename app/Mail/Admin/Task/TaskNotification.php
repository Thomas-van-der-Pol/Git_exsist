<?php

namespace App\Mail\Admin\Task;

use Illuminate\Bus\Queueable;
use KJ\Core\mail\BaseMail;
use KJLocalization;

class TaskNotification extends BaseMail
{
    use Queueable;

    public $item;
    public $url;

    /**
     * Create a notification instance.
     *
     * @param  string $token
     */
    public function __construct($item)
    {
        $this->item = $item;
        $this->url = url(route('admin.task.detail', ['ID' => $item->ID]));
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->from(config('mail.from.address'), config('mail.from.name'))
            ->subject(KJLocalization::translate('E-mails', 'Taak toegewezen', 'Taak toegewezen'))
            ->embed('logo', public_path('/assets/custom/img/logos/mail/logo.png'))
            ->markdown('admin.mails.task.notification', [
                'item' => $this->item,
                'url' => $this->url
            ]);

        return $email;
    }
}