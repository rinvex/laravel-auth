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

use Illuminate\Contracts\Auth\Authenticatable as BaseAuthenticatable;

interface AuthenticatableContract extends BaseAuthenticatable
{
    /**
     * Get the Two-Factor options.
     *
     * @return array
     */
    public function getTwoFactor();

    /**
     * Get the email address used for Two-Factor authentication.
     *
     * @return string
     */
    public function getEmailForTwoFactorAuth();

    /**
     * Get the country code used for Two-Factor authentication.
     *
     * @return string
     */
    public function getCountryCodeForTwoFactorAuth();

    /**
     * Get the phone number used for Two-Factor authentication.
     *
     * @return string
     */
    public function getPhoneForTwoFactorAuth();

    /**
     * Get the Two-Factor options in array format.
     *
     * @param string $options
     *
     * @return array
     */
    public function getTwoFactorAttribute($options);

    /**
     * Set the Two-Factor options in array format.
     *
     * @param array $options
     *
     * @return void
     */
    public function setTwoFactorAttribute(array $options);
}
