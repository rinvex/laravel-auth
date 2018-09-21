<?php

declare(strict_types=1);

namespace Rinvex\Auth\Services;

use Closure;
use Illuminate\Support\Arr;
use UnexpectedValueException;
use Illuminate\Contracts\Auth\UserProvider;
use Rinvex\Auth\Contracts\CanVerifyEmailContract;
use Rinvex\Auth\Contracts\EmailVerificationBrokerContract;

class EmailVerificationBroker implements EmailVerificationBrokerContract
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
     * {@inheritdoc}
     */
    public function sendVerificationLink(array $credentials): string
    {
        // First we will check to see if we found a user at the given credentials and
        // if we did not we will redirect back to this current URI with a piece of
        // "flash" data in the session to indicate to the developers the errors.
        if (is_null($user = $this->getUser($credentials))) {
            return static::INVALID_USER;
        }

        $expiration = now()->addMinutes($this->expiration)->timestamp;

        // Once we have the verification token, we are ready to send the message out to
        // this user with a link to verify their email. We will then redirect back to
        // the current URI having nothing set in the session to indicate any errors
        $user->sendEmailVerificationNotification($this->createToken($user, $expiration), $expiration);

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

        // Once the email has been verified, we'll call the given
        // callback, then we'll delete the token and return.
        // in their persistent storage.
        $callback($user);

        return static::EMAIL_VERIFIED;
    }

    /**
     * Get the user for the given credentials.
     *
     * @param array $credentials
     *
     * @throws \UnexpectedValueException
     *
     * @return \Rinvex\Auth\Contracts\CanVerifyEmailContract|null
     */
    public function getUser(array $credentials): ?CanVerifyEmailContract
    {
        $user = $this->users->retrieveByCredentials(Arr::only($credentials, ['email']));

        if ($user && ! $user instanceof CanVerifyEmailContract) {
            throw new UnexpectedValueException('User must implement CanVerifyEmailContract interface.');
        }

        return $user;
    }

    /**
     * Create a new email verification token for the given user.
     *
     * @param \Rinvex\Auth\Contracts\CanVerifyEmailContract $user
     * @param int                                           $expiration
     *
     * @return string
     */
    public function createToken(CanVerifyEmailContract $user, $expiration): string
    {
        $payload = $this->buildPayload($user, $user->getEmailForVerification(), $expiration);

        return hash_hmac('sha256', $payload, $this->getKey());
    }

    /**
     * Validate the given email verification token.
     *
     * @param \Rinvex\Auth\Contracts\CanVerifyEmailContract $user
     * @param array                                         $credentials
     *
     * @return bool
     */
    public function validateToken(CanVerifyEmailContract $user, array $credentials): bool
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
        return now()->createFromTimestamp($expiration)->isFuture();
    }

    /**
     * Return the application key.
     *
     * @return string
     */
    public function getKey(): string
    {
        if (starts_with($this->key, 'base64:')) {
            return base64_decode(mb_substr($this->key, 7));
        }

        return $this->key;
    }

    /**
     * Returns the payload string containing.
     *
     * @param \Rinvex\Auth\Contracts\CanVerifyEmailContract $user
     * @param string                                        $email
     * @param int                                           $expiration
     *
     * @return string
     */
    protected function buildPayload(CanVerifyEmailContract $user, $email, $expiration): string
    {
        return implode(';', [
            $email,
            $expiration,
            $user->getKey(),
            $user->password,
        ]);
    }

    /**
     * Validate an email verification for the given credentials.
     *
     * @param array $credentials
     *
     * @return \Rinvex\Auth\Contracts\CanVerifyEmailContract|string
     */
    protected function validateVerification(array $credentials)
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
