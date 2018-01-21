<?php

declare(strict_types=1);

namespace Rinvex\Fort\Traits;

use Rinvex\Fort\Notifications\EmailVerificationNotification;

trait CanVerifyEmail
{
    /**
     * {@inheritdoc}
     */
    public function getEmailForVerification(): string
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmailVerified(): bool
    {
        return (bool) $this->email_verified;
    }

    /**
     * {@inheritdoc}
     */
    public function sendEmailVerificationNotification($token, $expiration): void
    {
        $this->notify(new EmailVerificationNotification($token, $expiration));
    }
}
