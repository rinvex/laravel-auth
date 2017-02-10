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

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class VerificationSuccessNotification extends Notification
{
    /**
     * Indicates if the user is active.
     *
     * @var bool
     */
    public $active;

    /**
     * Create a notification instance.
     *
     * @param bool $social
     */
    public function __construct($active = false)
    {
        $this->active = $active;
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
        if ($this->active) {
            $phrase = trans('rinvex/fort::emails.verification.email.success.intro_default');
        } else {
            $phrase = trans('rinvex/fort::emails.verification.email.success.intro_moderation');
        }

        return (new MailMessage())
            ->subject(trans('rinvex/fort::emails.verification.email.success.subject'))
            ->line($phrase);
    }
}
