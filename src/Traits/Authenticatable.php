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

use Rinvex\Country\Models\Country;
use Illuminate\Auth\Authenticatable as BaseAuthenticatable;

trait Authenticatable
{
    use BaseAuthenticatable;

    /**
     * Get the Two-Factor options.
     *
     * @return array
     */
    public function getTwoFactor()
    {
        return $this->two_factor;
    }

    /**
     * Get the email address used for Two-Factor authentication.
     *
     * @return string
     */
    public function getEmailForTwoFactorAuth()
    {
        return $this->email;
    }

    /**
     * Get the country code used for Two-Factor authentication.
     *
     * @return string
     */
    public function getCountryCodeForTwoFactorAuth()
    {
        return Country::find($this->country)['dialling']['calling_code'][0];
    }

    /**
     * Get the phone number used for Two-Factor authentication.
     *
     * @return string
     */
    public function getPhoneForTwoFactorAuth()
    {
        return $this->phone;
    }

    /**
     * Get the Two-Factor options in array format.
     *
     * @param  string $options
     *
     * @return array
     */
    public function getTwoFactorAttribute($options)
    {
        return json_decode($options, true) ?: [];
    }

    /**
     * Set the Two-Factor options in array format.
     *
     * @param  array $options
     *
     * @return void
     */
    public function setTwoFactorAttribute(array $options)
    {
        $this->attributes['two_factor'] = json_encode($options);
    }
}
