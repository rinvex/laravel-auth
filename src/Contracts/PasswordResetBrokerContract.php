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

use Closure;

interface PasswordResetBrokerContract
{
    /**
     * Constant representing a successfully sent password reset.
     *
     * @var string
     */
    const LINK_SENT = 'rinvex/fort::messages.passwordreset.sent';

    /**
     * Constant representing a successfully processed password reset.
     *
     * @var string
     */
    const RESET_SUCCESS = 'rinvex/fort::messages.passwordreset.success';

    /**
     * Constant representing an invalid user.
     *
     * @var string
     */
    const INVALID_USER = 'rinvex/fort::messages.passwordreset.invalid_user';

    /**
     * Constant representing an invalid password.
     *
     * @var string
     */
    const INVALID_PASSWORD = 'rinvex/fort::messages.passwordreset.invalid_password';

    /**
     * Constant representing an invalid token.
     *
     * @var string
     */
    const INVALID_TOKEN = 'rinvex/fort::messages.passwordreset.invalid_token';

    /**
     * Constant representing an valid token.
     *
     * @var string
     */
    const VALID_TOKEN = 'rinvex/fort::messages.passwordreset.valid_token';

    /**
     * Send a password reset link to a user.
     *
     * @param array $credentials
     *
     * @return string
     */
    public function send(array $credentials);

    /**
     * Reset the password for the given credentials.
     *
     * @param array         $credentials
     * @param \Closure|null $callback
     *
     * @return mixed
     */
    public function reset(array $credentials, Closure $callback = null);

    /**
     * Set a custom password validator.
     *
     * @param \Closure $callback
     *
     * @return void
     */
    public function validator(Closure $callback);

    /**
     * Determine if the passwords match for the request.
     *
     * @param array $credentials
     *
     * @return bool
     */
    public function validateNewPassword(array $credentials);
}
