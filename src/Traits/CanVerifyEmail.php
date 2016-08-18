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

namespace Rinvex\Fort\Traits;

trait CanVerifyEmail
{
    /**
     * Get the email address where verification links are sent.
     *
     * @return string
     */
    public function getEmailForVerification()
    {
        return $this->email;
    }

    /**
     * Determine if email is verified or not.
     *
     * @return bool
     */
    public function isEmailVerified()
    {
        return (bool) $this->email_verified;
    }
}
