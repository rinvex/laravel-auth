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

interface TwoFactorProviderContract
{
    /**
     * Register the given user with the provider.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     *
     * @throws \NotificationChannels\Authy\Exceptions\InvalidConfiguration
     *
     * @return int
     */
    public function register(AuthenticatableContract $user);

    /**
     * Determine if the given token is valid for the given user.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     * @param string                                         $token
     * @param bool                                           $force
     *
     * @throws \NotificationChannels\Authy\Exceptions\InvalidConfiguration
     *
     * @return bool
     */
    public function tokenIsValid(AuthenticatableContract $user, $token, $force = true);

    /**
     * Delete the given user from the provider.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     *
     * @throws \NotificationChannels\Authy\Exceptions\InvalidConfiguration
     *
     * @return bool
     */
    public function delete(AuthenticatableContract $user);
}
