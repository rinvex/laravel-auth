<?php

/*
 * NOTICE OF LICENSE
 *
 * Part of the Rinvex Fort Package.
 *
 * This source file is subject to The MIT License (MIT)
 * that is bundled with this package in the LICENSE file.
 *
 * Package: Rinvex Fort Package
 * License: The MIT License (MIT)
 * Link:    https://rinvex.com
 */

namespace Rinvex\Fort\Notifications;

use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class VerificationSuccessNotification extends Notification
{
    /**
     * Indicates if the user is moderated or not.
     *
     * @var bool
     */
    public $moderated;

    /**
     * Create a notification instance.
     *
     * @param bool $social
     *
     * @return void
     */
    public function __construct($moderated = false)
    {
        $this->moderated = $moderated;
    }

    /**
     * Get the notification's channels.
     *
     * @param mixed $notifiable
     *
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail()
    {
        if ($this->moderated) {
            $phrase = Lang::get('rinvex.fort::email.verification.email.success.intro_moderation');
        } else {
            $phrase = Lang::get('rinvex.fort::email.verification.email.success.intro_default');
        }

        return (new MailMessage())
            ->subject(Lang::get('rinvex.fort::email.verification.email.success.subject'))
            ->line($phrase);
    }
}
