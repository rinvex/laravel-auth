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
use NotificationChannels\Authy\AuthyChannel;
use NotificationChannels\Authy\AuthyMessage;

class PhoneVerificationRequestNotification extends Notification
{
    /**
     * Determine whether to force the notification over cellphone network.
     *
     * @var bool
     */
    public $force;

    /**
     * The notification method (sms/call).
     *
     * @var string
     */
    public $method;

    /**
     * Create a notification instance.
     *
     * @param bool   $force
     * @param string $method
     *
     * @return void
     */
    public function __construct($force, $method)
    {
        $this->force  = $force;
        $this->method = $method;
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
        return AuthyMessage::create()->method($this->method)->force($this->force);
    }
}
