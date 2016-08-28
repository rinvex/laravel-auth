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
use Rinvex\Fort\Contracts\UserRepositoryContract;
use Rinvex\Fort\Contracts\CanVerifyEmailContract;
use Rinvex\Fort\Contracts\AuthenticatableContract;
use Rinvex\Fort\Contracts\VerificationBrokerContract;
use Rinvex\Fort\Contracts\VerificationTokenRepositoryContract;

class VerificationBroker implements VerificationBrokerContract
{
    /**
     * The verification token repository.
     *
     * @var \Rinvex\Fort\Contracts\VerificationTokenRepositoryContract
     */
    protected $tokens;

    /**
     * The user provider implementation.
     *
     * @var \Rinvex\Fort\Contracts\UserRepositoryContract
     */
    protected $users;

    /**
     * Create a new verification broker instance.
     *
     * @param \Rinvex\Fort\Contracts\VerificationTokenRepositoryContract $tokens
     * @param \Rinvex\Fort\Contracts\UserRepositoryContract              $users
     *
     * @return void
     */
    public function __construct(VerificationTokenRepositoryContract $tokens, UserRepositoryContract $users)
    {
        $this->users  = $users;
        $this->tokens = $tokens;
    }

    /**
     * {@inheritdoc}
     */
    public function sendPhoneVerification(AuthenticatableContract $user, $method)
    {
        $authy = app(TwoFactorAuthyProvider::class);

        // Register user with Authy
        $registered = $authy->register($user);

        // Refetch user instance again after authy registration
        $user = app('rinvex.fort.user')->find($user->id);

        // Determine which method to use
        $method = $method === 'call' ? 'sendPhoneCallToken' : 'sendSmsToken';

        // Send auth token
        $sent = $authy->$method($user);

        return $registered && $sent;
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

        // Fire the email verification request start event
        event('rinvex.fort.verification.email.request.start', [$user]);

        // Once we have the verification token, we are ready to send the message out
        // to this user with a link for verification. We will then redirect back to
        // the current URI having nothing set in the session to indicate errors.
        $token      = $this->tokens->getData($user, $this->tokens->create($user));
        $expiration = $this->tokens->getExpiration() / 60;

        $user->sendEmailVerificationNotification($token, $expiration);

        // Fire the email verification request success event
        event('rinvex.fort.verification.email.request.success', [$user]);

        return static::REQUEST_SENT;
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
        event('rinvex.fort.verification.email.verify.start', [$user]);

        // Verify email
        app('rinvex.fort.user')->update($user->id, [
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
        event('rinvex.fort.verification.email.verify.success', [$user]);

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

        $user = $this->users->findByCredentials($credentials);

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
     * @return \Rinvex\Fort\Contracts\VerificationTokenRepositoryContract
     */
    public function getTokenRepository()
    {
        return $this->tokens;
    }
}
