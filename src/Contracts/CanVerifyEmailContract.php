<?php

declare(strict_types=1);

namespace Rinvex\Fort\Contracts;

interface CanVerifyEmailContract
{
    /**
     * Get the email for verification sending.
     *
     * @return string
     */
    public function getEmailForVerification(): string;

    /**
     * Determine if email is verified or not.
     *
     * @return bool
     */
    public function isEmailVerified(): bool;

    /**
     * Send the email verification notification.
     *
     * @param string $token
     * @param string $expiration
     *
     * @return void
     */
    public function sendEmailVerificationNotification($token, $expiration): void;
}
