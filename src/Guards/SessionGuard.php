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

namespace Rinvex\Fort\Guards;

use Carbon\Carbon;
use Rinvex\Fort\Models\Persistence;
use Rinvex\Fort\Traits\ThrottlesLogins;
use Rinvex\Fort\Services\TwoFactorTotpProvider;
use Illuminate\Auth\Events\Logout as LogoutEvent;
use Illuminate\Auth\SessionGuard as BaseSessionGuard;
use Rinvex\Fort\Exceptions\InvalidPersistenceException;
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
    const AUTH_LOGIN = 'rinvex/fort::messages.auth.login';

    /**
     * Constant representing a failed login.
     *
     * @var string
     */
    const AUTH_FAILED = 'rinvex/fort::messages.auth.failed';

    /**
     * Constant representing an unverified user.
     *
     * @var string
     */
    const AUTH_UNVERIFIED = 'rinvex/fort::messages.auth.unverified';

    /**
     * Constant representing a locked out user.
     *
     * @var string
     */
    const AUTH_LOCKED_OUT = 'rinvex/fort::messages.auth.lockout';

    /**
     * Constant representing a user with Two-Factor authentication enabled.
     *
     * @var string
     */
    const AUTH_TWOFACTOR_REQUIRED = 'rinvex/fort::messages.verification.twofactor.totp.required';

    /**
     * Constant representing a user with Two-Factor failed authentication.
     *
     * @var string
     */
    const AUTH_TWOFACTOR_FAILED = 'rinvex/fort::messages.verification.twofactor.invalid_token';

    /**
     * Constant representing a user with phone verified.
     *
     * @var string
     */
    const AUTH_PHONE_VERIFIED = 'rinvex/fort::messages.verification.phone.verified';

    /**
     * Constant representing a logged out user.
     *
     * @var string
     */
    const AUTH_LOGOUT = 'rinvex/fort::messages.auth.logout';

    /**
     * Indicates if there's logout attempt.
     *
     * @var bool
     */
    protected $logoutAttempted = false;

    /**
     * Get the currently authenticated user.
     *
     * @throws \Rinvex\Fort\Exceptions\InvalidPersistenceException
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if ($this->loggedOut) {
            return;
        }

        // If we've already retrieved the user for the current request we can just
        // return it back immediately. We do not want to fetch the user data on
        // every call to this method because that would be tremendously slow.
        if (! is_null($this->user)) {
            return $this->user;
        }

        $id = $this->session->get($this->getName());

        // First we will try to load the user using the identifier in the session if
        // one exists. Otherwise we will check for a "remember me" cookie in this
        // request, and if one exists, attempt to retrieve the user using that.
        $user = null;

        if (! is_null($id)) {
            if ($user = $this->provider->retrieveById($id)) {
                $this->fireAuthenticatedEvent($user);
            }
        }

        // If the user is null, but we decrypt a "recaller" cookie we can attempt to
        // pull the user data on that cookie which serves as a remember cookie on
        // the application. Once we have a user we can return it to the caller.
        $recaller = $this->recaller();

        if (is_null($user) && ! is_null($recaller)) {
            $user = $this->userFromRecaller($recaller);

            if ($user) {
                // Copy the old session id for persistence update
                // before the `updateSession` method change it!
                $oldSession = $this->session->getId();

                $this->updateSession($user->getAuthIdentifier());

                // Update user persistence
                $this->updatePersistence($user->id, $oldSession, false);

                $this->fireLoginEvent($user, true);
            }
        }

        $persistence = null;

        // Check if we've a valid persistence
        if ($user && ! $this->logoutAttempted && ! ($persistence = $this->getPersistenceByToken($this->session->getId()))) {
            $this->logout();

            // Throw invalid persistence exception
            throw new InvalidPersistenceException();
        }

        // Update last activity
        if (! $this->logoutAttempted && ! is_null($user) && ! is_null($persistence)) {
            $persistence->touch();
        }

        return $this->user = $user;
    }

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
        $credentials = $credentials + ['active' => true];

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

            $totp = array_get($user->getTwoFactor(), 'totp.enabled');
            $phone = array_get($user->getTwoFactor(), 'phone.enabled');

            // Enforce Two-Factor authentication
            if ($totp || $phone) {
                // Update user persistence
                $this->updatePersistence($user->id, $this->session->getId(), true);

                $this->session->flash('rinvex.fort.twofactor.user', $user);
                $this->session->flash('rinvex.fort.twofactor.remember', $remember);
                $this->session->flash('rinvex.fort.twofactor.persistence', $this->session->getId());
                $this->session->flash('rinvex.fort.twofactor.methods', ['totp' => $totp, 'phone' => $phone]);

                // Fire the Two-Factor authentication required event
                $this->events->dispatch('rinvex.fort.twofactor.required', [$user]);

                return static::AUTH_TWOFACTOR_REQUIRED;
            }

            // If Two-Factor enabled, `attempt` method always returns false,
            // use `login` or `loginUsingId` methods to login users in such case.
            return $this->login($user, $remember);
        }

        // Invalid credentials, increment login attempts
        if ($throttles && ! $lockedOut) {
            $this->incrementLoginAttempts($this->getRequest());
        }

        // Clear Two-Factor authentication attempts
        $this->clearTwoFactor();

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
     * @param string                                     $persistence
     *
     * @return string
     */
    public function login(AuthenticatableContract $user, $remember = false, $persistence = null)
    {
        $this->updateSession($user->getAuthIdentifier());

        // If the user should be permanently "remembered" by the application we will
        // queue a permanent cookie that contains the encrypted copy of the user
        // identifier. We will then decrypt this later to retrieve the users.
        if ($remember) {
            $this->ensureRememberTokenIsSet($user);

            $this->queueRecallerCookie($user);
        }

        // Check persistence mode
        if (config('rinvex.fort.persistence') === 'single') {
            $user->persistences()->delete();
        }

        // Update user last login timestamp
        $user->update(['login_at' => new Carbon()]);

        // Update user persistence
        $this->updatePersistence($user->id, $persistence ?: $this->session->getId(), false);

        // Login successful, clear login attempts
        if (config('rinvex.fort.throttle.enabled')) {
            $this->clearLoginAttempts($this->getRequest());
        }

        // Clear Two-Factor authentication attempts
        $this->clearTwoFactor();

        // If we have an event dispatcher instance set we will fire an event so that
        // any listeners will hook into the authentication events and run actions
        // based on the login and logout events fired from the guard instances.
        $this->fireLoginEvent($user, $remember);

        $this->setUser($user);

        return static::AUTH_LOGIN;
    }

    /**
     * Log the user out of the application.
     *
     * @return void
     */
    public function logout()
    {
        $this->logoutAttempted = true;

        $user = $this->user();

        // If we have an event dispatcher instance, we can fire off the logout event
        // so any further processing can be done. This allows the developer to be
        // listening for anytime a user signs out of this application manually.
        $this->clearUserDataFromStorage();

        if (! is_null($this->user)) {
            $this->cycleRememberToken($user);
        }

        // Delete user persistence
        if ($persistence = Persistence::find($this->session->getId())) {
            $persistence->delete();
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
        if (! empty($session = $this->session->get('rinvex.fort.twofactor.persistence')) && $persistence = $this->getPersistenceByToken($session)) {
            return $this->provider->retrieveById($persistence->user_id);
        }
    }

    /**
     * Clear Two-Factor authentication attempts.
     *
     * @return void
     */
    protected function clearTwoFactor()
    {
        $this->session->forget([
            'rinvex.fort.twofactor.user',
            'rinvex.fort.twofactor.methods',
            'rinvex.fort.twofactor.remember',
            'rinvex.fort.twofactor.persistence',
        ]);
    }

    /**
     * Remember Two-Factor authentication attempts.
     *
     * @return void
     */
    public function rememberTwoFactor()
    {
        $this->session->keep([
            'rinvex.fort.twofactor.user',
            'rinvex.fort.twofactor.methods',
            'rinvex.fort.twofactor.remember',
            'rinvex.fort.twofactor.persistence',
        ]);
    }

    /**
     * Update user persistence.
     *
     * @param int    $id
     * @param string $token
     * @param bool   $attempt
     *
     * @return void
     */
    protected function updatePersistence($id, $token, $attempt)
    {
        $agent = request()->server('HTTP_USER_AGENT');
        $ip = request()->ip();

        // Delete previous user persistence
        if ($persistence = Persistence::find($token)) {
            $persistence->delete();
        }

        // Create new user persistence
        Persistence::create([
            'user_id' => $id,
            'token' => $this->session->getId(),
            'attempt' => $attempt,
            'agent' => $agent,
            'ip' => $ip,
        ]);
    }

    /**
     * Verify Two-Factor authentication.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param string                                     $token
     *
     * @return string
     */
    public function attemptTwoFactor(AuthenticatableContract $user, $token)
    {
        // Prepare required variables
        $validBackup = false;

        // Verify Two-Factor authentication
        if ($this->session->get('rinvex.fort.twofactor.persistence') && ($this->isValidTwoFactorTotp($user, $token) || $this->isValidTwoFactorPhone($user, $token) || $validBackup = $this->isValidTwoFactorBackup($user, $token))) {
            if ($validBackup) {
                $this->invalidateTwoFactorBackup($user, $token);
            }

            return static::AUTH_LOGIN;
        }

        // This is NOT login attempt, it's just account update -> phone verification
        if (! $this->session->get('rinvex.fort.twofactor.persistence') && $this->isValidTwoFactorPhone($user, $token)) {
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

        // Update Two-Factor OTP backup codes
        $user->update([
            'two_factor' => $settings,
        ]);
    }

    /**
     * Determine if the given token is a valid Two-Factor Phone token.
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

        return strlen($token) === 7 && app('rinvex.authy.token')->verify($token, $authyId);
    }

    /**
     * Determine if the given token is a valid Two-Factor Backup code.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableTwoFactorContract $user
     * @param                                                         $token
     *
     * @return bool
     */
    protected function isValidTwoFactorBackup(AuthenticatableTwoFactorContract $user, $token)
    {
        $backup = array_get($user->getTwoFactor(), 'totp.backup', []);

        return strlen($token) === 10 && in_array($token, $backup);
    }

    /**
     * Determine if the given token is a valid Two-Factor TOTP token.
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

        return strlen($token) === 6 && isset($this->session->get('rinvex.fort.twofactor.methods')['totp']) && $totp->verifyKey($secret, $token);
    }

    /**
     * Pull a persistence from the repository by its token.
     *
     * @param string $token
     *
     * @return \Rinvex\Fort\Models\Persistence
     */
    public function getPersistenceByToken($token)
    {
        return Persistence::where('token', $token)->first();
    }
}
