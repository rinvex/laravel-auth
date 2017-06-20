<?php

declare(strict_types=1);

namespace Rinvex\Fort\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class VerificationSuccessNotification extends Notification implements ShouldQueue
{
    use Queueable;

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
     * @param  mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if ($this->active) {
            $phrase = trans('emails.verification.email.success.intro_default');
        } else {
            $phrase = trans('emails.verification.email.success.intro_moderation');
        }

        return (new MailMessage())
            ->subject(trans('emails.verification.email.success.subject'))
            ->line($phrase);
    }
}
