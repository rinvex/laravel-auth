<?php

declare(strict_types=1);

namespace Rinvex\Fort\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class EmailVerificationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The email verification token.
     *
     * @var string
     */
    public $token;

    /**
     * The email verification expiration date.
     *
     * @var int
     */
    public $expiration;

    /**
     * Create a notification instance.
     *
     * @param string $token
     * @param string $expiration
     */
    public function __construct($token, $expiration)
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
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $email = $notifiable->getEmailForVerification();
        $link = route('frontend.verification.email.verify')."?email={$email}&expiration={$this->expiration}&token={$this->token}";

        return (new MailMessage())
            ->subject(trans('emails.verification.email.subject'))
            ->line(trans('emails.verification.email.intro', ['expire' => Carbon::createFromTimestamp($this->expiration)->diffForHumans()]))
            ->action(trans('emails.verification.email.action'), $link)
            ->line(trans('emails.verification.email.outro'));
    }
}
