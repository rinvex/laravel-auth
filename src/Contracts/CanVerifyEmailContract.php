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
    public function getEmailForVerification();

    /**
     * Determine if email is verified or not.
     *
     * @return bool
     */
    public function isEmailVerified();

    /**
     * Send the email verification notification.
     *
     * @param array  $token
     * @param string $expiration
     *
     * @return void
     */
    public function sendEmailVerificationNotification(array $token, $expiration);
}
