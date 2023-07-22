<?php

declare(strict_types=1);

namespace Rinvex\Auth\Services;

use Closure;
use Illuminate\Support\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use UnexpectedValueException;
use Illuminate\Contracts\Auth\UserProvider;
use Rinvex\Auth\Contracts\CanResetPasswordContract;
use Rinvex\Auth\Contracts\PasswordResetBrokerContract;

class PasswordResetBroker implements PasswordResetBrokerContract
{
    /**
     * The application key.
     *
     * @var string
     */
    protected $key;

    /**
     * The user provider implementation.
     *
     * @var \Illuminate\Contracts\Auth\UserProvider
     */
    protected $users;

    /**
     * The number of minutes that the reset token should be considered valid.
     *
     * @var int
     */
    protected $expiration;

    /**
     * Create a new verification broker instance.
     *
     * @param \Illuminate\Contracts\Auth\UserProvider $users
     * @param string                                  $key
     * @param int                                     $expiration
     */
    public function __construct(UserProvider $users, $key, $expiration)
    {
        $this->key = $key;
        $this->users = $users;
        $this->expiration = $expiration;
    }

    /**
     * Send a password reset link to a user.
     *
     * @param array         $credentials
     * @param \Closure|null $callback
     *
     * @return string
     */
    public function sendResetLink(array $credentials, Closure $callback = null)
    {
        // First we will check to see if we found a user at the given credentials and
        // if we did not we will redirect back to this current URI with a piece of
        // "flash" data in the session to indicate to the developers the errors.
        $user = $this->getUser($credentials);

        if (is_null($user)) {
            return static::INVALID_USER;
        }

        $expiration = Carbon::now()->addMinutes($this->expiration)->timestamp;

        // Once we have the reset token, we are ready to send the message out to this
        // user with a link to reset their password. We will then redirect back to
        // the current URI having nothing set in the session to indicate errors.
        $user->sendPasswordResetNotification($this->createToken($user, $expiration), $expiration);

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
        $user = $this->validateReset($credentials);

        // If the responses from the validate method is not a user instance, we will
        // assume that it is a redirect and simply return it from this method and
        // the user is properly redirected having an error message on the post.
        if (! $user instanceof CanResetPasswordContract) {
            return $user;
        }

        $password = $credentials['password'];

        // Once the reset has been validated, we'll call the given callback with the
        // new password. This gives the user an opportunity to store the password
        // in their persistent storage.
        $callback($user, $password);

        return static::PASSWORD_RESET;
    }

    /**
     * Get the user for the given credentials.
     *
     * @param array $credentials
     *
     * @throws \UnexpectedValueException
     *
     * @return \Rinvex\Auth\Contracts\CanResetPasswordContract|null
     */
    public function getUser(array $credentials): ?CanResetPasswordContract
    {
        $user = $this->users->retrieveByCredentials(Arr::only($credentials, ['email']));

        if ($user && ! $user instanceof CanResetPasswordContract) {
            throw new UnexpectedValueException('User must implement CanResetPassword interface.');
        }

        return $user;
    }

    /**
     * Create a new password reset token for the given user.
     *
     * @param \Rinvex\Auth\Contracts\CanResetPasswordContract $user
     * @param int                                             $expiration
     *
     * @return string
     */
    public function createToken(CanResetPasswordContract $user, $expiration): string
    {
        $payload = $this->buildPayload($user, $user->getEmailForPasswordReset(), $expiration);

        return hash_hmac('sha256', $payload, $this->getKey());
    }

    /**
     * Validate the given password reset token.
     *
     * @param \Rinvex\Auth\Contracts\CanResetPasswordContract $user
     * @param array                                           $credentials
     *
     * @return bool
     */
    public function validateToken(CanResetPasswordContract $user, array $credentials): bool
    {
        $payload = $this->buildPayload($user, $credentials['email'], $credentials['expiration']);

        return hash_equals($credentials['token'], hash_hmac('sha256', $payload, $this->getKey()));
    }

    /**
     * Validate the given expiration timestamp.
     *
     * @param int $expiration
     *
     * @return bool
     */
    public function validateTimestamp($expiration): bool
    {
        return Carbon::now()->createFromTimestamp($expiration)->isFuture();
    }

    /**
     * Return the application key.
     *
     * @return string
     */
    public function getKey(): string
    {
        if (Str::startsWith($this->key, 'base64:')) {
            return base64_decode(mb_substr($this->key, 7));
        }

        return $this->key;
    }

    /**
     * Returns the payload string containing.
     *
     * @param \Rinvex\Auth\Contracts\CanResetPasswordContract $user
     * @param string                                          $email
     * @param int                                             $expiration
     *
     * @return string
     */
    protected function buildPayload(CanResetPasswordContract $user, $email, $expiration): string
    {
        return implode(';', [
            $email,
            $expiration,
            $user->getKey(),
            $user->password,
        ]);
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

        if (! $this->validateToken($user, $credentials)) {
            return static::INVALID_TOKEN;
        }

        if (! $this->validateTimestamp($credentials['expiration'])) {
            return static::EXPIRED_TOKEN;
        }

        return $user;
    }
}
