<?php

declare(strict_types=1);

namespace Rinvex\Auth\Contracts;

interface EmailVerificationBrokerFactoryContract
{
    /**
     * Get a broker instance by name.
     *
     * @param string $name
     *
     * @return \Rinvex\Auth\Contracts\EmailVerificationBrokerContract
     */
    public function broker($name = null);
}
