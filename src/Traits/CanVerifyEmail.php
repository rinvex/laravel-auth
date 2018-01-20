<?php

declare(strict_types=1);

namespace Rinvex\Fort\Traits;

use Rinvex\Fort\Notifications\EmailVerificationNotification;

trait CanVerifyEmail
{
    /**
     * Get the email address where verification links are sent.
     *
     * @return string
     */
    public function getEmailForVerification(): string
    {
        return $this->email;
    }

    /**
     * Determine if email is verified or not.
     *
     * @return bool
     */
    public function isEmailVerified(): bool
    {
        return (bool) $this->email_verified;
    }

    /**
     * Send the email verification notification.
     *
     * @param string $token
     * @param string $expiration
     *
     * @return void
     */
    public function sendEmailVerificationNotification($token, $expiration): void
    {
        $this->notify(new EmailVerificationNotification($token, $expiration));
    }
}
