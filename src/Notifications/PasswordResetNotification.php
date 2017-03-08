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

declare(strict_types=1);

namespace Rinvex\Fort\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PasswordResetNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The password reset token.
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
     */
    public function __construct(array $token, $expiration)
    {
        $this->token = $token;
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
            ->subject(trans('emails.passwordreset.request.subject'))
            ->line(trans('emails.passwordreset.request.intro', ['expire' => $this->expiration]))
            ->action(trans('emails.passwordreset.request.action'), route('frontend.passwordreset.reset').'?token='.$this->token['token'].'&email='.$this->token['email'])
            ->line(trans('emails.passwordreset.request.outro', [
                'ip' => $this->token['ip'],
                'agent' => $this->token['agent'],
                'created_at' => $this->token['created_at'],
            ]));
    }
}
