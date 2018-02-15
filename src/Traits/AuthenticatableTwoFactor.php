<?php

declare(strict_types=1);

namespace Rinvex\Auth\Traits;

trait AuthenticatableTwoFactor
{
    /**
     * Get the TwoFactor options.
     *
     * @return array|null
     */
    public function getTwoFactor(): ?array
    {
        return $this->two_factor;
    }
}
