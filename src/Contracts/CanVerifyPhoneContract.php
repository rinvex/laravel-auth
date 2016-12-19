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

interface CanVerifyPhoneContract
{
    /**
     * Get the phone for verification.
     *
     * @return string
     */
    public function getPhoneForVerification();

    /**
     * Get the country for verification.
     *
     * @return string
     */
    public function getCountryForVerification();

    /**
     * Determine if phone is verified or not.
     *
     * @return bool
     */
    public function isPhoneVerified();

    /**
     * Send the phone verification notification.
     *
     * @param string $method
     * @param bool   $force
     *
     * @return void
     */
    public function sendPhoneVerificationNotification($method, $force);
}
