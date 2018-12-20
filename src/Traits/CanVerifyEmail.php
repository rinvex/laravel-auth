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
    public function hasVerifiedEmail(): bool
    {
        return ! is_null($this->email_verified_at);
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
