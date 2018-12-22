<?php

declare(strict_types=1);

namespace Rinvex\Auth\Contracts;

interface CanVerifyEmailContract
{
    /**
     * Get the email for verification sending.
     *
     * @return string
     */
    public function getEmailForVerification(): string;

    /**
     * Determine if the user has verified their email address.
     *
     * @return bool
     */
    public function hasVerifiedEmail(): bool;

    /**
     * Send the email verification notification.
     *
     * @param string $token
     * @param int    $expiration
     *
     * @return void
     */
    public function sendEmailVerificationNotification(string $token, int $expiration): void;
}
