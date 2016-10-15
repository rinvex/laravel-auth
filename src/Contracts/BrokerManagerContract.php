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

interface BrokerManagerContract
{
    /**
     * Get a broker instance by name.
     *
     * @param string $name
     *
     * @return \Rinvex\Fort\Contracts\PasswordResetBrokerContract|\Rinvex\Fort\Contracts\EmailVerificationBrokerContract
     */
    public function broker($name = null);
}
