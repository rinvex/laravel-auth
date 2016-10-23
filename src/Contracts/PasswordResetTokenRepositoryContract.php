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

interface PasswordResetTokenRepositoryContract
{
    /**
     * Create a new token.
     *
     * @param \Rinvex\Fort\Contracts\CanResetPasswordContract $user
     *
     * @return string
     */
    public function create(CanResetPasswordContract $user);

    /**
     * Determine if a token record exists and is valid.
     *
     * @param \Rinvex\Fort\Contracts\CanResetPasswordContract $user
     * @param string                                          $token
     *
     * @return bool
     */
    public function exists(CanResetPasswordContract $user, $token);

    /**
     * Delete a reset token record.
     *
     * @param string $token
     *
     * @return void
     */
    public function delete($token);

    /**
     * Delete expired reset tokens.
     *
     * @return void
     */
    public function deleteExpired();

    /**
     * Get the token expiration in minutes.
     *
     * @return int
     */
    public function getExpiration();

    /**
     * Get password reset token data.
     *
     * @param \Rinvex\Fort\Contracts\CanResetPasswordContract $user
     * @param string                                          $token
     *
     * @return array
     */
    public function getData(CanResetPasswordContract $user, $token);
}
