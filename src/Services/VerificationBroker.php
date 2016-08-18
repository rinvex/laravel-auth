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
use Illuminate\Mail\Message;
use UnexpectedValueException;
use Illuminate\Support\Facades\Lang;
use Illuminate\Contracts\Mail\Mailer;
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
     * The mailer instance.
     *
     * @var \Illuminate\Contracts\Mail\Mailer
     */
    protected $mailer;

    /**
     * The view of the verification link email.
     *
     * @var string
     */
    protected $emailView;

    /**
     * Create a new verification broker instance.
     *
     * @param \Rinvex\Fort\Contracts\VerificationTokenRepositoryContract $tokens
     * @param \Rinvex\Fort\Contracts\UserRepositoryContract              $users
     * @param \Illuminate\Contracts\Mail\Mailer                          $mailer
     * @param string                                                     $emailView
     *
     * @return void
     */
    public function __construct(VerificationTokenRepositoryContract $tokens, UserRepositoryContract $users, Mailer $mailer, $emailView)
    {
        $this->users     = $users;
        $this->mailer    = $mailer;
        $this->tokens    = $tokens;
        $this->emailView = $emailView;
    }

    /**
     * Send Two-Factor Token.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     * @param string                                         $method
     *
     * @return bool
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
    public function sendVerificationLink(array $credentials, Closure $callback = null)
    {
        // First we will check to see if we found a user at the given credentials and
        // if we did not we will redirect back to this current URI with a piece of
        // "flash" data in the session to indicate to the developers the errors.
        if (is_null($user = $this->getUser($credentials))) {
            return static::INVALID_USER;
        }

        // Fire the email verification request start event
        event('rinvex.fort.verification.email.request.start', [$user, $this->mailer]);

        // Once we have the verification token, we are ready to send the message out
        // to this user with a link for verification. We will then redirect back to
        // the current URI having nothing set in the session to indicate errors.
        $token = $this->createToken($user);

        // We will use verification email view that was given to the broker.
        // We'll pass a "token" variable into the view so that it may
        // be displayed for an user to click for verification.
        $view       = $this->emailView;
        $expiration = $this->tokens->getExpiration() / 60;
        $tokenData  = $this->tokens->getData($user, $token);
        $email      = function (Message $message) use ($user, $token, $callback) {
            $message->to($user->getEmailForVerification());
            $message->subject(Lang::get('rinvex.fort::email.verification.subject'));

            if (! is_null($callback)) {
                call_user_func($callback, $message, $user, $token);
            }
        };

        $this->mailer->send($view, compact('token', 'user', 'expiration', 'tokenData'), $email);

        // Fire the email verification request success event
        event('rinvex.fort.verification.email.request.success', [$user, $this->mailer]);

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
        event('rinvex.fort.verification.email.verify.start', [$user, $this->mailer]);

        // Verify email
        app('rinvex.fort.user')->update($user->id, [
            'email_verified'    => true,
            'email_verified_at' => new Carbon,
        ]);

        // Once we have called this callback, we will remove this token row from the
        // table and return the response from this callback so the user gets sent
        // to the destination given by the developers from the callback return.
        if (! is_null($callback)) {
            call_user_func($callback, $user);
        }

        $this->deleteToken($credentials['token']);

        // Fire the email verification success event
        event('rinvex.fort.verification.email.verify.success', [$user, $this->mailer]);

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
     * Create a new verification token for the given user.
     *
     * @param  \Rinvex\Fort\Contracts\CanVerifyEmailContract $user
     *
     * @return string
     */
    public function createToken(CanVerifyEmailContract $user)
    {
        return $this->tokens->create($user);
    }

    /**
     * Delete the given verification token.
     *
     * @param  string $token
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
     * @param  \Rinvex\Fort\Contracts\CanVerifyEmailContract $user
     * @param  string                                        $token
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
