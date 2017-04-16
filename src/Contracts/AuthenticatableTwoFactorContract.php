<?php

declare(strict_types=1);

namespace Rinvex\Fort\Contracts;

interface AuthenticatableTwoFactorContract
{
    /**
     * Get the Two-Factor options.
     *
     * @return array
     */
    public function getTwoFactor();

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
