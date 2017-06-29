<?php

declare(strict_types=1);

namespace Rinvex\Fort\Traits;

use Rinvex\Fort\Notifications\PasswordResetNotification;

trait CanResetPassword
{
    /**
     * Get the email address where password reset links are sent.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->email;
    }

    /**
     * Send the password reset notification.
     *
     * @param string $token
     * @param string $expiration
     *
     * @return void
     */
    public function sendPasswordResetNotification($token, $expiration)
    {
        $this->notify(new PasswordResetNotification($token, $expiration));
    }
}
