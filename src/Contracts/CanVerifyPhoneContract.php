<?php

declare(strict_types=1);

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
