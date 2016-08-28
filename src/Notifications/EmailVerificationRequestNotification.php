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

class EmailVerificationRequestNotification extends Notification
{
    /**
     * The email verification token.
     *
     * @var string
     */
    public $token;

    /**
     * The password reset token expiration.
     *
     * @var string
     */
    public $expiration;

    /**
     * Create a notification instance.
     *
     * @param array  $token
     * @param string $expiration
     *
     * @return void
     */
    public function __construct(array $token, $expiration)
    {
        $this->token      = $token;
        $this->expiration = $expiration;
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
        return (new MailMessage())
            ->subject(Lang::get('rinvex.fort::email.verification.email.subject'))
            ->line(Lang::get('rinvex.fort::email.verification.email.intro'))
            ->action(Lang::get('rinvex.fort::email.verification.email.action'), route('rinvex.fort.verification.email.verify').'?token='.$this->token['token'].'&email='.$this->token['email'])
            ->line(Lang::get('rinvex.fort::email.verification.email.outro', [
                'created_at' => $this->token['created_at'],
                'ip'         => $this->token['ip'],
                'agent'      => $this->token['agent'],
            ]));
    }
}
