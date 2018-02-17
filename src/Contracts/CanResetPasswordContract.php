<?php

declare(strict_types=1);

namespace Rinvex\Auth\Contracts;

interface CanResetPasswordContract
{
    /**
     * Get the email address where password reset links are sent.
     *
     * @return string
     */
    public function getEmailForPasswordReset(): string;

    /**
     * Send the password reset notification.
     *
     * @param string $token
     * @param string $expiration
     *
     * @return void
     */
    public function sendPasswordResetNotification($token, $expiration): void;
}
