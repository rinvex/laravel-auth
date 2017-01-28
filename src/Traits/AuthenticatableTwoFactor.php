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

trait AuthenticatableTwoFactor
{
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
     * Get the Two-Factor options in array format.
     *
     * @param string $options
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
     * @param array $options
     *
     * @return void
     */
    public function setTwoFactorAttribute(array $options)
    {
        $this->attributes['two_factor'] = json_encode($options);
    }
}
