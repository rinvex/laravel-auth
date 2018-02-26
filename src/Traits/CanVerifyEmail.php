<?php

declare(strict_types=1);

namespace Rinvex\Auth\Traits;

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
        return $this->email_verified;
    }

    /**
     * {@inheritdoc}
     */
    public function sendEmailVerificationNotification(string $token, int $expiration): void
    {
        ! $this->emailVerificationNotificationClass
        || $this->notify(new $this->emailVerificationNotificationClass($token, $expiration));
    }
}
