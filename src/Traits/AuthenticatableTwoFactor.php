<?php

declare(strict_types=1);

namespace Rinvex\Fort\Traits;

trait AuthenticatableTwoFactor
{
    /**
     * Get the TwoFactor options.
     *
     * @return array
     */
    public function getTwoFactor()
    {
        return $this->two_factor;
    }

    /**
     * Get the TwoFactor options in array format.
     *
     * @param string $options
     *
     * @return array
     */
    public function getTwoFactorAttribute($options)
    {
        return $options ? json_decode($options, true) : [];
    }

    /**
     * Set the TwoFactor options in array format.
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
