<?php

declare(strict_types=1);

namespace Rinvex\Fort\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use NotificationChannels\Authy\AuthyChannel;
use NotificationChannels\Authy\AuthyMessage;

class PhoneVerificationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The notification method (sms/call).
     *
     * @var string
     */
    public $method;

    /**
     * Determine whether to force the notification over cellphone network.
     *
     * @var bool
     */
    public $force;

    /**
     * Create a notification instance.
     *
     * @param string $method
     * @param bool   $force
     */
    public function __construct($method = 'sms', $force = false)
    {
        $this->method = $method;
        $this->force = $force;
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
        return [AuthyChannel::class];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @return \NotificationChannels\Authy\AuthyMessage
     */
    public function toAuthy()
    {
        $message = AuthyMessage::create()->method($this->method);

        if ($this->force) {
            $message->force();
        }

        return $message;
    }
}
