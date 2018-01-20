<?php

declare(strict_types=1);

namespace Rinvex\Fort\Contracts;

interface AuthenticatableTwoFactorContract
{
    /**
     * Get the TwoFactor options.
     *
     * @return array|null
     */
    public function getTwoFactor(): ?array;
}
