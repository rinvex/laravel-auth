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

interface TwoFactorSmsTokenContract
{
    /**
     * Send the user Two-Factor authentication token via SMS.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     * @param bool                                           $force
     *
     * @return void
     */
    public function sendSmsToken(AuthenticatableContract $user, $force = false);
}
