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
use Illuminate\Support\Str;
use Illuminate\Mail\Message;
use UnexpectedValueException;
use Illuminate\Support\Facades\Lang;
use Illuminate\Contracts\Mail\Mailer;
use Rinvex\Fort\Contracts\ResetBrokerContract;
use Rinvex\Fort\Contracts\UserRepositoryContract;
use Rinvex\Fort\Contracts\CanResetPasswordContract;
use Rinvex\Fort\Contracts\ResetTokenRepositoryContract;

class ResetBroker implements ResetBrokerContract
{
    /**
     * The reset password token repository.
     *
     * @var \Rinvex\Fort\Contracts\ResetTokenRepositoryContract
     */
    protected $tokens;

    /**
     * The user provider.
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
     * The view of the password reset link email.
     *
     * @var string
     */
    protected $emailView;

    /**
     * The custom password validator callback.
     *
     * @var \Closure
     */
    protected $passwordValidator;

    /**
     * Create a new password broker instance.
     *
     * @param \Rinvex\Fort\Contracts\ResetTokenRepositoryContract $tokens
     * @param \Rinvex\Fort\Contracts\UserRepositoryContract       $users
     * @param \Illuminate\Contracts\Mail\Mailer                   $mailer
     * @param string                                              $emailView
     *
     * @return void
     */
    public function __construct(ResetTokenRepositoryContract $tokens, UserRepositoryContract $users, Mailer $mailer, $emailView)
    {
        $this->users     = $users;
        $this->mailer    = $mailer;
        $this->tokens    = $tokens;
        $this->emailView = $emailView;
    }

    /**
     * {@inheritdoc}
     */
    public function sendResetLink(array $credentials, Closure $callback = null)
    {
        // First we will check to see if we found a user at the given credentials and
        // if we did not we will redirect back to this current URI with a piece of
        // "flash" data in the session to indicate to the developers the errors.
        if (is_null($user = $this->getUser($credentials))) {
            return static::INVALID_USER;
        }

        // Fire the password request start event
        event('rinvex.fort.password.request.start', [$user, $this->mailer]);

        // Once we have the reset password token, we are ready to send the message out
        // to this user with a link for password. We will then redirect back to the
        // current URI having nothing set in the session to indicate errors.
        $token = $this->createToken($user);

        // We will use reset password email view that was given to the broker.
        // We'll pass a "token" variable into the view so that it may
        // be displayed for an user to click for password reset.
        $view       = $this->emailView;
        $expiration = $this->tokens->getExpiration() / 60;
        $tokenData  = $this->tokens->getData($user, $token);
        $email      = function (Message $message) use ($user, $token, $callback) {
            $message->to($user->getEmailForPasswordReset());
            $message->subject(Lang::get('rinvex.fort::email.password.subject'));

            if (! is_null($callback)) {
                call_user_func($callback, $message, $user, $token);
            }
        };

        $this->mailer->send($view, compact('token', 'user', 'expiration', 'tokenData'), $email);

        // Fire the password request sent event
        event('rinvex.fort.password.request.success', [$user, $this->mailer]);

        return static::REQUEST_SENT;
    }

    /**
     * {@inheritdoc}
     */
    public function reset(array $credentials, Closure $callback = null)
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

        if (! $this->validateNewPassword($credentials)) {
            return static::INVALID_PASSWORD;
        }

        // Fire the password reset start event
        event('rinvex.fort.password.reset.start', [$user, $this->mailer]);

        // Update user password
        app('rinvex.fort.user')->update($user->id, [
            'password'       => bcrypt($credentials['password']),
            'remember_token' => Str::random(60),
        ]);

        // Once we have called this callback, we will remove this token row from the
        // table and return the response from this callback so the user gets sent
        // to the destination given by the developers from the callback return.
        if (! is_null($callback)) {
            call_user_func($callback, $user, $credentials['password']);
        }

        $this->deleteToken($credentials['token']);

        // Fire the password reset success event
        event('rinvex.fort.password.reset.success', [$user, $this->mailer]);

        return static::RESET_SUCCESS;
    }

    /**
     * {@inheritdoc}
     */
    public function validator(Closure $callback)
    {
        $this->passwordValidator = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function validateNewPassword(array $credentials)
    {
        list($password, $confirm) = [
            $credentials['password'],
            $credentials['password_confirmation'],
        ];

        if (isset($this->passwordValidator)) {
            return call_user_func($this->passwordValidator, $credentials) && $password === $confirm;
        }

        return $this->validatePasswordWithDefaults($credentials);
    }

    /**
     * Determine if the passwords are valid for the request.
     *
     * @param array $credentials
     *
     * @return bool
     */
    protected function validatePasswordWithDefaults(array $credentials)
    {
        list($password, $confirm) = [
            $credentials['password'],
            $credentials['password_confirmation'],
        ];

        return $password === $confirm && mb_strlen($password) >= 6;
    }

    /**
     * Get the user for the given credentials.
     *
     * @param array $credentials
     *
     * @throws \UnexpectedValueException
     *
     * @return \Rinvex\Fort\Contracts\CanResetPasswordContract
     */
    public function getUser(array $credentials)
    {
        $credentials = Arr::except($credentials, ['token']);

        $user = $this->users->findByCredentials($credentials);

        if ($user && ! $user instanceof CanResetPasswordContract) {
            throw new UnexpectedValueException('User must implement CanResetPasswordContract interface.');
        }

        return $user;
    }

    /**
     * Create a new password reset token for the given user.
     *
     * @param \Rinvex\Fort\Contracts\CanResetPasswordContract $user
     *
     * @return string
     */
    public function createToken(CanResetPasswordContract $user)
    {
        return $this->tokens->create($user);
    }

    /**
     * Delete the given password reset token.
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
     * Validate the given password reset token.
     *
     * @param \Rinvex\Fort\Contracts\CanResetPasswordContract $user
     * @param string                                          $token
     *
     * @return bool
     */
    public function tokenExists(CanResetPasswordContract $user, $token)
    {
        return $this->tokens->exists($user, $token);
    }

    /**
     * Get the password reset token repository.
     *
     * @return \Rinvex\Fort\Contracts\ResetTokenRepositoryContract
     */
    public function getTokenRepository()
    {
        return $this->tokens;
    }

    /**
     * Validate the given password reset.
     *
     * @param array $credentials
     *
     * @return string
     */
    public function validateReset(array $credentials)
    {
        // Check given user
        if (is_null($user = $this->getUser(['email' => $credentials['email']]))) {
            return static::INVALID_USER;
        }

        // Check given token
        if (! $this->tokenExists($user, $credentials['token'])) {
            return static::INVALID_TOKEN;
        }

        // All is well, continue password reset
        return static::VALID_TOKEN;
    }
}
