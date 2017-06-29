<?php

declare(strict_types=1);

namespace Rinvex\Fort\Contracts;

interface EmailVerificationBrokerFactoryContract
{
    /**
     * Get a broker instance by name.
     *
     * @param string $name
     *
     * @return \Rinvex\Fort\Contracts\EmailVerificationBrokerContract
     */
    public function broker($name = null);
}
