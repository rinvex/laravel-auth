<?php

/*
 * NOTICE OF LICENSE
 *
 * Part of the Rinvex Fort Package.
 *
 * This source file is subject to The MIT License (MIT)
 * that is bundled with this package in the LICENSE file.
 *
 * Package: Rinvex Fort Package
 * License: The MIT License (MIT)
 * Link:    https://rinvex.com
 */

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
