<?php

declare(strict_types=1);

namespace Rinvex\Auth\Traits;

trait CanResetPassword
{
    /**
     * {@inheritdoc}
     */
    public function getEmailForPasswordReset(): string
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function sendPasswordResetNotification(string $token, int $expiration): void
    {
        ! $this->passwordResetNotificationClass
        || $this->notify(new $this->passwordResetNotificationClass($token, $expiration));
    }
}
