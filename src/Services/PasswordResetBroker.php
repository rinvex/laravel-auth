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
use Rinvex\Fort\Contracts\CanResetPasswordContract;
use Rinvex\Fort\Contracts\PasswordResetTokenRepositoryContract;
use Illuminate\Contracts\Auth\PasswordBroker as PasswordBrokerContract;

class PasswordResetBroker implements PasswordBrokerContract
{
    /**
     * The password token repository.
     *
     * @var \Illuminate\Auth\Passwords\TokenRepositoryInterface
     */
    protected $tokens;

    /**
     * The user provider implementation.
     *
     * @var \Illuminate\Contracts\Auth\UserProvider
     */
    protected $users;

    /**
     * The custom password validator callback.
     *
     * @var \Closure
     */
    protected $passwordValidator;

    /**
     * Create a new password broker instance.
     *
     * @param \Rinvex\Fort\Contracts\PasswordResetTokenRepositoryContract $tokens
     * @param \Illuminate\Contracts\Auth\UserProvider                     $users
     */
    public function __construct(PasswordResetTokenRepositoryContract $tokens, UserProvider $users)
    {
        $this->users = $users;
        $this->tokens = $tokens;
    }

    /**
     * Send a password reset link to a user.
     *
     * @param array $credentials
     *
     * @return string
     */
    public function sendResetLink(array $credentials)
    {
        // First we will check to see if we found a user at the given credentials and
        // if we did not we will redirect back to this current URI with a piece of
        // "flash" data in the session to indicate to the developers the errors.
        $user = $this->getUser($credentials);

        if (is_null($user)) {
            return static::INVALID_USER;
        }

        // Once we have the reset password token, we are ready to send the message out
        // to this user with a link for password. We will then redirect back to the
        // current URI having nothing set in the session to indicate errors.
        $data = $this->tokens->getData($user, $token = $this->tokens->create($user));
        $expiration = $this->tokens->getExpiration();

        // Returned token is hashed, and we need the
        // public token to be sent to the user
        $data['token'] = $token;

        $user->sendPasswordResetNotification($data, $expiration);

        return static::RESET_LINK_SENT;
    }

    /**
     * Reset the password for the given token.
     *
     * @param array    $credentials
     * @param \Closure $callback
     *
     * @return mixed
     */
    public function reset(array $credentials, Closure $callback)
    {
        // If the responses from the validate method is not a user instance, we will
        // assume that it is a redirect and simply return it from this method and
        // the user is properly redirected having an error message on the post.
        $user = $this->validateReset($credentials);

        if (! $user instanceof CanResetPasswordContract) {
            return $user;
        }

        $password = $credentials['password'];

        // Once the reset has been validated, we'll call the given callback with the
        // new password. This gives the user an opportunity to store the password
        // in their persistent storage. Then we'll delete the token and return.
        $callback($user, $password);

        $this->tokens->delete($user);

        return static::PASSWORD_RESET;
    }

    /**
     * Set a custom password validator.
     *
     * @param \Closure $callback
     *
     * @return void
     */
    public function validator(Closure $callback)
    {
        $this->passwordValidator = $callback;
    }

    /**
     * Determine if the passwords match for the request.
     *
     * @param array $credentials
     *
     * @return bool
     */
    public function validateNewPassword(array $credentials)
    {
        if (isset($this->passwordValidator)) {
            list($password, $confirm) = [
                $credentials['password'],
                $credentials['password_confirmation'],
            ];

            return call_user_func(
                $this->passwordValidator, $credentials
            ) && $password === $confirm;
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

        $user = $this->users->retrieveByCredentials($credentials);

        if ($user && ! $user instanceof CanResetPasswordContract) {
            throw new UnexpectedValueException('User must implement CanResetPassword interface.');
        }

        return $user;
    }

    /**
     * Create a new password reset token for the given user.
     *
     * @param CanResetPasswordContract $user
     *
     * @return string
     */
    public function createToken(CanResetPasswordContract $user)
    {
        return $this->tokens->create($user);
    }

    /**
     * Delete password reset tokens of the given user.
     *
     * @param \Illuminate\Contracts\Auth\CanResetPassword $user
     *
     * @return void
     */
    public function deleteToken(CanResetPasswordContract $user)
    {
        $this->tokens->delete($user);
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
     * Get the password reset token repository implementation.
     *
     * @return \Illuminate\Auth\Passwords\TokenRepositoryInterface
     */
    public function getRepository()
    {
        return $this->tokens;
    }

    /**
     * Validate a password reset for the given credentials.
     *
     * @param array $credentials
     *
     * @return \Illuminate\Contracts\Auth\CanResetPassword|string
     */
    protected function validateReset(array $credentials)
    {
        if (is_null($user = $this->getUser($credentials))) {
            return static::INVALID_USER;
        }

        if (! $this->validateNewPassword($credentials)) {
            return static::INVALID_PASSWORD;
        }

        if (! $this->tokens->exists($user, $credentials['token'])) {
            return static::INVALID_TOKEN;
        }

        return $user;
    }
}
