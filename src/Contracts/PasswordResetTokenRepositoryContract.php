<?php

declare(strict_types=1);

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
     * Delete tokens of the given user.
     *
     * @param \Rinvex\Fort\Contracts\CanResetPasswordContract $user
     *
     * @return void
     */
    public function delete(CanResetPasswordContract $user);

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
     *
     * @return array
     */
    public function getData(CanResetPasswordContract $user);
}
