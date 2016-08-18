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

interface TwoFactorPhoneTokenContract
{
    /**
     * Send the user Two-Factor authentication token via phone call.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     * @param boolean                                        $force
     *
     * @return void
     */
    public function sendPhoneCallToken(AuthenticatableContract $user, $force = true);
}
