<?php

declare(strict_types=1);

namespace Rinvex\Fort\Guards;

use Rinvex\Fort\Traits\ThrottlesLogins;
use Rinvex\Fort\Services\TwoFactorTotpProvider;
use Illuminate\Auth\Events\Logout as LogoutEvent;
use Illuminate\Auth\SessionGuard as BaseSessionGuard;
use Rinvex\Fort\Contracts\AuthenticatableTwoFactorContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class SessionGuard extends BaseSessionGuard
{
    use ThrottlesLogins;

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
     * Constant representing a locked out user.
     *
     * @var string
     */
    const AUTH_LOCKED_OUT = 'messages.auth.lockout';

    /**
     * Constant representing a user with TwoFactor authentication enabled.
     *
     * @var string
     */
    const AUTH_TWOFACTOR_REQUIRED = 'messages.verification.twofactor.totp.required';

    /**
     * Constant representing a user with TwoFactor failed authentication.
     *
     * @var string
     */
    const AUTH_TWOFACTOR_FAILED = 'messages.verification.twofactor.invalid_token';

    /**
     * Constant representing a user with phone verified.
     *
     * @var string
     */
    const AUTH_PHONE_VERIFIED = 'messages.verification.phone.verified';

    /**
     * Constant representing a logged out user.
     *
     * @var string
     */
    const AUTH_LOGOUT = 'messages.auth.logout';

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param array $credentials
     * @param bool  $remember
     *
     * @return string
     */
    public function attempt(array $credentials = [], $remember = false)
    {
        // Fire the authentication attempt event
        $this->fireAttemptEvent($credentials, $remember);

        $this->lastAttempted = $user = $this->provider->retrieveByCredentials($credentials);

        // Login throttling
        $throttles = config('rinvex.fort.throttle.enabled');
        $lockedOut = $this->hasTooManyLoginAttempts($this->getRequest());

        if ($throttles && $lockedOut) {
            // Fire the authentication lockout event (only if user exists)
            if ($user) {
                $this->fireLockoutEvent($this->getRequest());
            }

            return static::AUTH_LOCKED_OUT;
        }

        // If an implementation of UserInterface was returned, we'll ask the provider
        // to validate the user against the given credentials, and if they are in
        // fact valid we'll log the users into the application and return true.
        if ($this->hasValidCredentials($user, $credentials)) {
            // Check if unverified
            if (config('rinvex.fort.emailverification.required') && ! $user->isEmailVerified()) {
                // Fire the authentication unverified event
                $this->events->dispatch('rinvex.fort.auth.unverified', [$user]);

                // Verification required
                return static::AUTH_UNVERIFIED;
            }

            if (! empty(config('rinvex.fort.twofactor.providers'))) {
                $twofactor = $user->getTwoFactor();
                $totpStatus = $twofactor['totp']['enabled'] ?? false;
                $phoneStatus = $twofactor['phone']['enabled'] ?? false;

                // Enforce TwoFactor authentication
                if ($totpStatus || $phoneStatus) {
                    $this->session->put('_twofactor', ['user_id' => $user->id, 'remember' => $remember, 'totp' => $totpStatus, 'phone' => $phoneStatus]);

                    // Fire the TwoFactor authentication required event
                    $this->events->dispatch('rinvex.fort.twofactor.required', [$user]);

                    // If TwoFactor enabled, the attempt method never login users, so
                    // an explicit call to "login" or "loginUsingId" is required elsewhere
                    return static::AUTH_TWOFACTOR_REQUIRED;
                }
            }

            return $this->login($user, $remember);
        }

        // Invalid credentials, increment login attempts
        if ($throttles && ! $lockedOut) {
            $this->incrementLoginAttempts($this->getRequest());
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
    public function login(AuthenticatableContract $user, $remember = false)
    {
        // Check persistence mode
        if (config('rinvex.fort.persistence') === 'single') {
            $this->cycleRememberToken($user);
            $user->sessions()->delete();
        }

        $this->updateSession($user->getAuthIdentifier());

        // If the user should be permanently "remembered" by the application we will
        // queue a permanent cookie that contains the encrypted copy of the user
        // identifier. We will then decrypt this later to retrieve the users.
        if ($remember) {
            $this->ensureRememberTokenIsSet($user);

            $this->queueRecallerCookie($user);
        }

        // Login successful, clear login attempts
        if (config('rinvex.fort.throttle.enabled')) {
            $this->clearLoginAttempts($this->getRequest());
        }

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
    protected function updateSession($id)
    {
        $this->session->put($this->getName(), $id);
        $this->session->forget('_twofactor');
        $this->session->migrate(true);
    }

    /**
     * Log the user out of the application.
     *
     * @return void
     */
    public function logout()
    {
        $user = $this->user();

        // If we have an event dispatcher instance, we can fire off the logout event
        // so any further processing can be done. This allows the developer to be
        // listening for anytime a user signs out of this application manually.
        $this->clearUserDataFromStorage();

        if (! is_null($this->user)) {
            $this->cycleRememberToken($user);
        }

        if (isset($this->events)) {
            $this->events->dispatch(new LogoutEvent($user));
        }

        // Once we have fired the logout event we will clear the users out of memory
        // so they are no longer available as the user is no longer considered as
        // being signed into this application and should not be available here.
        $this->user = null;

        $this->loggedOut = true;

        return static::AUTH_LOGOUT;
    }

    /**
     * Return login attempt user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|object|null
     */
    public function attemptUser()
    {
        if (! empty($twofactor = $this->session->get('_twofactor'))) {
            return $this->provider->retrieveById($twofactor['user_id']);
        }
    }

    /**
     * Verify TwoFactor authentication.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableTwoFactorContract $user
     * @param string                                                  $token
     *
     * @return string
     */
    public function attemptTwoFactor(AuthenticatableTwoFactorContract $user, $token)
    {
        // Verify TwoFactor authentication
        if ($this->session->has('_twofactor') && ($this->isValidTwoFactorTotp($user, $token) || $this->isValidTwoFactorBackup($user, $token) || $this->isValidTwoFactorPhone($user, $token))) {
            return static::AUTH_LOGIN;
        }

        // This is NOT login attempt, it's just account update -> phone verification
        if (! $this->session->has('_twofactor') && $this->isValidTwoFactorPhone($user, $token)) {
            return static::AUTH_PHONE_VERIFIED;
        }

        return static::AUTH_TWOFACTOR_FAILED;
    }

    /**
     * Invalidate given backup code for the given user.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableTwoFactorContract $user
     * @param                                                         $token
     *
     * @return void
     */
    protected function invalidateTwoFactorBackup(AuthenticatableTwoFactorContract $user, $token)
    {
        $settings = $user->getTwoFactor();
        $backup = array_get($settings, 'totp.backup');

        unset($backup[array_search($token, $backup)]);

        array_set($settings, 'totp.backup', $backup);

        // Update TwoFactor OTP backup codes
        $user->fill(['two_factor' => $settings])->forceSave();
    }

    /**
     * Determine if the given token is a valid TwoFactor Phone token.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableTwoFactorContract $user
     * @param                                                         $token
     *
     * @return bool
     */
    protected function isValidTwoFactorPhone(AuthenticatableTwoFactorContract $user, $token)
    {
        $settings = $user->getTwoFactor();
        $authyId = array_get($settings, 'phone.authy_id');

        return in_array(mb_strlen($token), [6, 7, 8]) && app('rinvex.authy.token')->verify($token, $authyId)->succeed();
    }

    /**
     * Determine if the given token is a valid TwoFactor Backup code.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableTwoFactorContract $user
     * @param                                                         $token
     *
     * @return bool
     */
    protected function isValidTwoFactorBackup(AuthenticatableTwoFactorContract $user, $token)
    {
        $backup = array_get($user->getTwoFactor(), 'totp.backup', []);
        $result = mb_strlen($token) === 10 && in_array($token, $backup);
        ! $result || $this->invalidateTwoFactorBackup($user, $token);

        return $result;
    }

    /**
     * Determine if the given token is a valid TwoFactor TOTP token.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableTwoFactorContract $user
     * @param                                                         $token
     *
     * @return bool
     */
    protected function isValidTwoFactorTotp(AuthenticatableTwoFactorContract $user, $token)
    {
        $totp = app(TwoFactorTotpProvider::class);
        $secret = array_get($user->getTwoFactor(), 'totp.secret');

        return mb_strlen($token) === 6 && $this->session->get('_twofactor.totp') && $totp->verifyKey($secret, $token);
    }
}
