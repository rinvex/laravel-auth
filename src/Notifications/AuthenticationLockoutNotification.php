<?php

declare(strict_types=1);

namespace Rinvex\Fort\Notifications;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AuthenticationLockoutNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    public $request;

    /**
     * Create a notification instance.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
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
        return (new MailMessage())
            ->subject(trans('emails.auth.lockout.subject'))
            ->line(trans('emails.auth.lockout.intro', [
                'created_at' => new Carbon(),
                'ip' => $this->request->ip(),
                'agent' => $this->request->server('HTTP_USER_AGENT'),
            ]))
            ->line(trans('emails.auth.lockout.outro'));
    }
}
