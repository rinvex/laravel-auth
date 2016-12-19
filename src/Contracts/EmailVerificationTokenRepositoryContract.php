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

interface EmailVerificationTokenRepositoryContract
{
    /**
     * Create a new verification token.
     *
     * @param \Rinvex\Fort\Contracts\CanVerifyEmailContract $user
     *
     * @return string
     */
    public function create(CanVerifyEmailContract $user);

    /**
     * Determine if a verification token record exists and is valid.
     *
     * @param \Rinvex\Fort\Contracts\CanVerifyEmailContract $user
     * @param string                                        $token
     *
     * @return bool
     */
    public function exists(CanVerifyEmailContract $user, $token);

    /**
     * Delete a verification token record.
     *
     * @param string $token
     *
     * @return void
     */
    public function delete($token);

    /**
     * Delete expired verification tokens.
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
     * Get email verification token data.
     *
     * @param \Rinvex\Fort\Contracts\CanVerifyEmailContract $user
     * @param string                                        $token
     *
     * @return array
     */
    public function getData(CanVerifyEmailContract $user, $token);
}
