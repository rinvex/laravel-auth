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

use Rinvex\Country\Loader;
use Rinvex\Fort\Notifications\PhoneVerificationRequestNotification;

trait CanVerifyPhone
{
    /**
     * Get the phone for verification.
     *
     * @return string
     */
    public function getPhoneForVerification()
    {
        return $this->phone;
    }

    /**
     * Get the country for verification.
     *
     * @return string
     */
    public function getCountryForVerification()
    {
        $country = Loader::country($this->country);

        return $country ? $country->getCallingCode() : null;
    }

    /**
     * Determine if phone is verified or not.
     *
     * @return bool
     */
    public function isPhoneVerified()
    {
        return (bool) $this->phone_verified;
    }

    /**
     * Send the phone verification notification.
     *
     * @param bool   $force
     * @param string $method
     *
     * @return void
     */
    public function sendPhoneVerificationNotification($force, $method)
    {
        $this->notify(new PhoneVerificationRequestNotification($force, $method));
    }
}
