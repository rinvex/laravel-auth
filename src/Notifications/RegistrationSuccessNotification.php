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

class RegistrationSuccessNotification extends Notification
{
    /**
     * Indicates if this is social registration mode or not.
     *
     * @var bool
     */
    public $social;

    /**
     * Create a notification instance.
     *
     * @param bool $social
     *
     * @return void
     */
    public function __construct($social = false)
    {
        $this->social = $social;
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
        if ($this->social) {
            if (config('rinvex.fort.registration.moderated')) {
                $phrase = Lang::get('rinvex.fort::email.registration.welcome.intro_moderation');
            } else {
                $phrase = Lang::get('rinvex.fort::email.registration.welcome.intro_default');
            }
        } else {
            if (config('rinvex.fort.verification.required') && config('rinvex.fort.registration.moderated')) {
                $phrase = Lang::get('rinvex.fort::email.registration.welcome.intro_verification_moderation');
            } elseif (! config('rinvex.fort.verification.required') && config('rinvex.fort.registration.moderated')) {
                $phrase = Lang::get('rinvex.fort::email.registration.welcome.intro_moderation');
            } elseif (config('rinvex.fort.verification.required') && ! config('rinvex.fort.registration.moderated')) {
                $phrase = Lang::get('rinvex.fort::email.registration.welcome.intro_verification');
            } else {
                $phrase = Lang::get('rinvex.fort::email.registration.welcome.intro_default');
            }
        }

        return (new MailMessage())
            ->subject(Lang::get('rinvex.fort::email.registration.welcome.subject'))
            ->line($phrase);
    }
}
