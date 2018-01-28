<?php

declare(strict_types=1);

namespace Rinvex\Fort\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RegistrationSuccessNotification extends Notification implements ShouldQueue
{
    use Queueable;

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
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(trans('emails.register.welcome.subject'))
            ->line(config('rinvex.fort.registration.moderated')
                ? trans('emails.register.welcome.intro_moderation')
                : trans('emails.register.welcome.intro_default')
            );
    }
}
