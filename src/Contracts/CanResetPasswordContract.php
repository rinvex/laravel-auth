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
     * @param int    $expiration
     *
     * @return void
     */
    public function sendPasswordResetNotification(string $token, int $expiration): void;
}
