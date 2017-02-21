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
    protected $users;

    /**
     * Create a new verification broker instance.
     *
     * @param \Rinvex\Fort\Contracts\EmailVerificationTokenRepositoryContract $tokens
     * @param \Illuminate\Contracts\Auth\UserProvider                         $users
     */
    public function __construct(EmailVerificationTokenRepositoryContract $tokens, UserProvider $users)
    {
        $this->users = $users;
        $this->tokens = $tokens;
    }

    /**
     * {@inheritdoc}
     */
    public function sendVerificationLink(array $credentials)
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
        $data = $this->tokens->getData($user, $token = $this->tokens->create($user));
        $expiration = $this->tokens->getExpiration();

        // Returned token is hashed, and we need the
        // public token to be sent to the user
        $data['token'] = $token;

        $user->sendEmailVerificationNotification($data, $expiration);

        return static::LINK_SENT;
    }

    /**
     * {@inheritdoc}
     */
    public function verify(array $credentials, Closure $callback)
    {
        // If the responses from the validate method is not a user instance, we will
        // assume that it is a redirect and simply return it from this method and
        // the user is properly redirected having an error message on the post.
        $user = $this->validateVerification($credentials);

        if (! $user instanceof CanVerifyEmailContract) {
            return $user;
        }

        // Fire the email verification start event
        event('rinvex.fort.emailverification.start', [$user]);

        // Once the email has been verified, we'll call the given
        // callback, then we'll delete the token and return.
        $callback($user);

        $this->tokens->delete($user);

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

        $user = $this->users->retrieveByCredentials($credentials);

        if ($user && ! $user instanceof CanVerifyEmailContract) {
            throw new UnexpectedValueException('User must implement CanVerifyEmailContract interface.');
        }

        return $user;
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
    public function getRepository()
    {
        return $this->tokens;
    }

    /**
     * Validate an email verification for the given credentials.
     *
     * @param array $credentials
     *
     * @return \Rinvex\Fort\Contracts\CanVerifyEmailContract|string
     */
    protected function validateVerification(array $credentials)
    {
        if (is_null($user = $this->getUser($credentials))) {
            return static::INVALID_USER;
        }

        if (! $this->tokens->exists($user, $credentials['token'])) {
            return static::INVALID_TOKEN;
        }

        return $user;
    }
}
