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

namespace Rinvex\Fort\Services;

use Closure;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use UnexpectedValueException;
use Illuminate\Contracts\Auth\UserProvider;
use Rinvex\Fort\Contracts\CanVerifyEmailContract;
use Rinvex\Fort\Contracts\EmailVerificationBrokerContract;
use Rinvex\Fort\Contracts\EmailVerificationTokenRepositoryContract;

class EmailVerificationBroker implements EmailVerificationBrokerContract
{
    /**
     * The verification token repository.
     *
     * @var \Rinvex\Fort\Contracts\EmailVerificationTokenRepositoryContract
     */
    protected $tokens;

    /**
     * The user provider implementation.
     *
     * @var \Illuminate\Contracts\Auth\UserProvider
     */
    protected $userProvider;

    /**
     * Create a new verification broker instance.
     *
     * @param \Rinvex\Fort\Contracts\EmailVerificationTokenRepositoryContract $tokens
     * @param \Illuminate\Contracts\Auth\UserProvider                         $userProvider
     */
    public function __construct(EmailVerificationTokenRepositoryContract $tokens, UserProvider $userProvider)
    {
        $this->tokens = $tokens;
        $this->userProvider = $userProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function send(array $credentials)
    {
        // First we will check to see if we found a user at the given credentials and
        // if we did not we will redirect back to this current URI with a piece of
        // "flash" data in the session to indicate to the developers the errors.
        if (is_null($user = $this->getUser($credentials))) {
            return static::INVALID_USER;
        }

        // Once we have the verification token, we are ready to send the message out
        // to this user with a link for verification. We will then redirect back to
        // the current URI having nothing set in the session to indicate errors.
        $token = $this->tokens->getData($user, $this->tokens->create($user));
        $expiration = $this->tokens->getExpiration();

        $user->sendEmailVerificationNotification($token, $expiration);

        return static::LINK_SENT;
    }

    /**
     * {@inheritdoc}
     */
    public function verify(array $credentials, Closure $callback = null)
    {
        // If the responses from the validate method is not a user instance, we will
        // assume that it is a redirect and simply return it from this method and
        // the user is properly redirected having an error message on the post.
        if (is_null($user = $this->getUser($credentials))) {
            return static::INVALID_USER;
        }

        if (! $this->tokenExists($user, $credentials['token'])) {
            return static::INVALID_TOKEN;
        }

        // Fire the email verification start event
        event('rinvex.fort.emailverification.start', [$user]);

        // Verify email
        $user->update([
            'email_verified'    => true,
            'email_verified_at' => new Carbon(),
        ]);

        // Once we have called this callback, we will remove this token row from the
        // table and return the response from this callback so the user gets sent
        // to the destination given by the developers from the callback return.
        if (! is_null($callback)) {
            $callback($user);
        }

        $this->deleteToken($credentials['token']);

        // Fire the email verification success event
        event('rinvex.fort.emailverification.success', [$user]);

        return static::EMAIL_VERIFIED;
    }

    /**
     * Get the user for the given credentials.
     *
     * @param array $credentials
     *
     * @throws \UnexpectedValueException
     *
     * @return \Rinvex\Fort\Contracts\CanVerifyEmailContract
     */
    public function getUser(array $credentials)
    {
        $credentials = Arr::except($credentials, ['token']);

        $user = $this->userProvider->retrieveByCredentials($credentials);

        if ($user && ! $user instanceof CanVerifyEmailContract) {
            throw new UnexpectedValueException('User must implement CanVerifyEmailContract interface.');
        }

        return $user;
    }

    /**
     * Delete the given verification token.
     *
     * @param string $token
     *
     * @return void
     */
    public function deleteToken($token)
    {
        $this->tokens->delete($token);
    }

    /**
     * Validate the given verification token.
     *
     * @param \Rinvex\Fort\Contracts\CanVerifyEmailContract $user
     * @param string                                        $token
     *
     * @return bool
     */
    public function tokenExists(CanVerifyEmailContract $user, $token)
    {
        return $this->tokens->exists($user, $token);
    }

    /**
     * Get the verification token repository implementation.
     *
     * @return \Rinvex\Fort\Contracts\EmailVerificationTokenRepositoryContract
     */
    public function getTokenRepository()
    {
        return $this->tokens;
    }
}
