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
}
