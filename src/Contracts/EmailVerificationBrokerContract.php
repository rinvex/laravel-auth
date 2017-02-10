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

interface EmailVerificationBrokerContract
{
    /**
     * Constant representing a successfully sent verification email.
     *
     * @var string
     */
    const LINK_SENT = 'rinvex/fort::messages.verification.email.link_sent';

    /**
     * Constant representing a successfully verified email.
     *
     * @var string
     */
    const EMAIL_VERIFIED = 'rinvex/fort::messages.verification.email.verified';

    /**
     * Constant representing an invalid user.
     *
     * @var string
     */
    const INVALID_USER = 'rinvex/fort::messages.verification.email.invalid_user';

    /**
     * Constant representing an invalid token.
     *
     * @var string
     */
    const INVALID_TOKEN = 'rinvex/fort::messages.verification.email.invalid_token';

    /**
     * Send a user email verification.
     *
     * @param array $credentials
     *
     * @return string
     */
    public function send(array $credentials);

    /**
     * Verify given account.
     *
     * @param array         $credentials
     * @param \Closure|null $callback
     *
     * @return mixed
     */
    public function verify(array $credentials, Closure $callback = null);
}
