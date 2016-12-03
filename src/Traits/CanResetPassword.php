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
     * @param array  $token
     * @param string $expiration
     *
     * @return void
     */
    public function sendPasswordResetNotification(array $token, $expiration)
    {
        $this->notify(new PasswordResetNotification($token, $expiration));
    }
}
