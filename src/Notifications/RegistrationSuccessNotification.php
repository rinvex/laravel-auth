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
     * Indicates if this is social registration mode or not.
     *
     * @var bool
     */
    public $social;

    /**
     * Create a notification instance.
     *
     * @param bool $social
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
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if ($this->social) {
            if (config('rinvex.fort.registration.moderated')) {
                $phrase = trans('emails.register.welcome.intro_moderation');
            } else {
                $phrase = trans('emails.register.welcome.intro_default');
            }
        } else {
            if (config('rinvex.fort.emailverification.required') && config('rinvex.fort.registration.moderated')) {
                $phrase = trans('emails.register.welcome.intro_verification_moderation');
            } elseif (! config('rinvex.fort.emailverification.required') && config('rinvex.fort.registration.moderated')) {
                $phrase = trans('emails.register.welcome.intro_moderation');
            } elseif (config('rinvex.fort.emailverification.required') && ! config('rinvex.fort.registration.moderated')) {
                $phrase = trans('emails.register.welcome.intro_verification');
            } else {
                $phrase = trans('emails.register.welcome.intro_default');
            }
        }

        return (new MailMessage())
            ->subject(trans('emails.register.welcome.subject'))
            ->line($phrase);
    }
}
