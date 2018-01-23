<?php

declare(strict_types=1);

namespace Rinvex\Fort\Guards;

use Illuminate\Auth\SessionGuard as BaseSessionGuard;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class SessionGuard extends BaseSessionGuard
{
    /**
     * Constant representing a successful login.
     *
     * @var string
     */
    const AUTH_LOGIN = 'messages.auth.login';

    /**
     * Constant representing a failed login.
     *
     * @var string
     */
    const AUTH_FAILED = 'messages.auth.failed';

    /**
     * Constant representing an unverified user.
     *
     * @var string
     */
    const AUTH_UNVERIFIED = 'messages.auth.unverified';

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param array $credentials
     * @param bool  $remember
     *
     * @return string
     */
    public function attempt(array $credentials = [], $remember = false): string
    {
        // Fire the authentication attempt event
        $this->fireAttemptEvent($credentials, $remember);
        $socialLogin = array_get($credentials, 'social');
        $credentials = array_except($credentials, 'social');

        $this->lastAttempted = $user = $this->provider->retrieveByCredentials($credentials);

        // If an implementation of UserInterface was returned, we'll ask the provider
        // to validate the user against the given credentials, and if they are in
        // fact valid we'll log the users into the application and return true.
        if ($socialLogin || $this->hasValidCredentials($user, $credentials)) {
            // Check if unverified
            if (config('rinvex.fort.emailverification.required') && ! $user->isEmailVerified()) {
                // Fire the authentication unverified event
                $this->events->dispatch('rinvex.fort.auth.unverified', [$user]);

                // Verification required
                return static::AUTH_UNVERIFIED;
            }

            return $this->login($user, $remember);
        }

        // If the authentication attempt fails we will fire an event so that the user
        // may be notified of any suspicious attempts to access their account from
        // an unrecognized user. A developer may listen to this event as needed.
        $this->fireFailedEvent($user, $credentials);

        return static::AUTH_FAILED;
    }

    /**
     * Log a user into the application.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param bool                                       $remember
     *
     * @return string
     */
    public function login(AuthenticatableContract $user, $remember = false): string
    {
        $this->updateSession($user->getAuthIdentifier());

        // If the user should be permanently "remembered" by the application we will
        // queue a permanent cookie that contains the encrypted copy of the user
        // identifier. We will then decrypt this later to retrieve the users.
        if ($remember) {
            $this->ensureRememberTokenIsSet($user);

            $this->queueRecallerCookie($user);
        }

        // Login successful, clear login attempts
        $this->clearLoginAttempts($this->getRequest());

        // If we have an event dispatcher instance set we will fire an event so that
        // any listeners will hook into the authentication events and run actions
        // based on the login and logout events fired from the guard instances.
        $this->fireLoginEvent($user, $remember);

        $this->setUser($user);

        return static::AUTH_LOGIN;
    }

    /**
     * Update the session with the given ID.
     *
     * @param string $id
     *
     * @return void
     */
    protected function updateSession($id): void
    {
        $this->session->put($this->getName(), $id);
        $this->session->migrate(true);
    }
}
