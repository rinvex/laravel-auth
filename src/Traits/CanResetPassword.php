<?php

declare(strict_types=1);

namespace Rinvex\Fort\Traits;

use Rinvex\Fort\Notifications\PasswordResetNotification;

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
    public function sendPasswordResetNotification($token, $expiration): void
    {
        $this->notify(new PasswordResetNotification($token, $expiration));
    }
}
