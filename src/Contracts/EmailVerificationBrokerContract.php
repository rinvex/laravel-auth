<?php

declare(strict_types=1);

namespace Rinvex\Auth\Contracts;

use Closure;

interface EmailVerificationBrokerContract
{
    /**
     * Constant representing a successfully sent verification email.
     *
     * @var string
     */
    const LINK_SENT = 'messages.verification.email.link_sent';

    /**
     * Constant representing a successfully verified email.
     *
     * @var string
     */
    const EMAIL_VERIFIED = 'messages.verification.email.verified';

    /**
     * Constant representing an invalid user.
     *
     * @var string
     */
    const INVALID_USER = 'messages.verification.email.invalid_user';

    /**
     * Constant representing an invalid token.
     *
     * @var string
     */
    const INVALID_TOKEN = 'messages.verification.email.invalid_token';

    /**
     * Constant representing an expired token.
     *
     * @var string
     */
    const EXPIRED_TOKEN = 'messages.verification.email.expired_token';

    /**
     * Send a user email verification.
     *
     * @param array $credentials
     *
     * @return string
     */
    public function sendVerificationLink(array $credentials): string;

    /**
     * Verify given account.
     *
     * @param array         $credentials
     * @param \Closure|null $callback
     *
     * @return mixed
     */
    public function verify(array $credentials, Closure $callback);
}
