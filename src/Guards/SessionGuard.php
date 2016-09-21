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
use RuntimeException;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Auth\GuardHelpers;
use Rinvex\Fort\Traits\ThrottlesLogins;
use Illuminate\Session\SessionInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Rinvex\Fort\Services\TwoFactorTotpProvider;
use Rinvex\Fort\Services\TwoFactorAuthyProvider;
use Rinvex\Fort\Contracts\StatefulGuardContract;
use Illuminate\Contracts\Auth\SupportsBasicAuth;
use Rinvex\Fort\Contracts\UserRepositoryContract;
use Rinvex\Fort\Contracts\AuthenticatableContract;
use Rinvex\Fort\Exceptions\InvalidPersistenceException;
use Illuminate\Contracts\Cookie\QueueingFactory as CookieJar;

class SessionGuard implements StatefulGuardContract, SupportsBasicAuth
{
    use GuardHelpers, ThrottlesLogins;

    /**
     * Constant representing a successful login.
     *
     * @var string
     */
    const AUTH_VALID = 'rinvex.fort::message.auth.valid';

    /**
     * Constant representing a successful login.
     *
     * @var string
     */
    const AUTH_LOGIN = 'rinvex.fort::message.auth.login';

    /**
     * Constant representing a failed login.
     *
     * @var string
     */
    const AUTH_FAILED = 'rinvex.fort::message.auth.failed';

    /**
     * Constant representing an unverified user.
     *
     * @var string
     */
    const AUTH_UNVERIFIED = 'rinvex.fort::message.auth.unverified';

    /**
     * Constant representing a locked out user.
     *
     * @var string
     */
    const AUTH_LOCKED_OUT = 'rinvex.fort::message.auth.lockout';

    /**
     * Constant representing a user with Two-Factor authentication enabled.
     *
     * @var string
     */
    const AUTH_TWOFACTOR_REQUIRED = 'rinvex.fort::message.verification.twofactor.totp.required';

    /**
     * Constant representing a user with Two-Factor failed authentication.
     *
     * @var string
     */
    const AUTH_TWOFACTOR_FAILED = 'rinvex.fort::message.verification.twofactor.invalid_token';

    /**
     * Constant representing a user with phone verified.
     *
     * @var string
     */
    const AUTH_PHONE_VERIFIED = 'rinvex.fort::message.verification.phone.verified';

    /**
     * Constant representing a user with phone verified.
     *
     * @var string
     */
    const AUTH_REGISTERED = 'rinvex.fort::message.register.success';

    /**
     * Constant representing a logged out user.
     *
     * @var string
     */
    const AUTH_LOGOUT = 'rinvex.fort::message.auth.logout';

    /**
     * The name of the Guard. Typically "session".
     *
     * Corresponds to driver name in authentication configuration.
     *
     * @var string
     */
    protected $name;

    /**
     * The user we last attempted to retrieve.
     *
     * @var \Rinvex\Fort\Contracts\AuthenticatableContract
     */
    protected $lastAttempted;

    /**
     * Indicates if the user was authenticated via a recaller cookie.
     *
     * @var bool
     */
    protected $viaRemember = false;

    /**
     * The session used by the guard.
     *
     * @var \Illuminate\Session\SessionInterface
     */
    protected $session;

    /**
     * The Illuminate cookie creator service.
     *
     * @var \Illuminate\Contracts\Cookie\QueueingFactory
     */
    protected $cookie;

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * The event dispatcher instance.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * Indicates if the logout method has been called.
     *
     * @var bool
     */
    protected $loggedOut = false;

    /**
     * Indicates if there's logout attempt.
     *
     * @var bool
     */
    protected $logoutAttempted = false;

    /**
     * Indicates if a token user retrieval has been attempted.
     *
     * @var bool
     */
    protected $tokenRetrievalAttempted = false;

    /**
     * Create a new authentication guard.
     *
     * @param string                                        $name
     * @param \Rinvex\Fort\Contracts\UserRepositoryContract $provider
     * @param \Illuminate\Session\SessionInterface          $session
     * @param \Illuminate\Http\Request                      $request
     *
     * @return void
     */
    public function __construct($name, UserRepositoryContract $provider, SessionInterface $session, Request $request = null)
    {
        $this->name     = $name;
        $this->session  = $session;
        $this->request  = $request;
        $this->provider = $provider;
    }

    /**
     * Return login attempt user.
     *
     * @return \Rinvex\Fort\Contracts\AuthenticatableContract|object|null
     */
    public function attemptUser()
    {
        if (! empty($session = $this->session->get('rinvex.fort.twofactor.persistence')) && $persistence = $this->getPersistenceByToken($session)) {
            return $this->provider->find($persistence->user_id);
        }
    }

    /**
     * Get the currently authenticated user.
     *
     * @throws \Rinvex\Fort\Exceptions\InvalidPersistenceException
     *
     * @return null|\Rinvex\Fort\Contracts\AuthenticatableContract
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

        $userBySession = $this->getUserBySession();
        $userByCookie  = $this->getUserByCookie();

        // First we will try to load the user using the identifier in the session if
        // one exists. Otherwise we will check for a "remember me" cookie in this
        // request, and if one exists, attempt to retrieve the user using that.
        if ($userBySession) {
            // Fire the authenticated event
            $this->events->fire('rinvex.fort.auth.user', [$userBySession]);
        }

        // If the user is null, but we decrypt a "recaller" cookie we can attempt to
        // pull the user data on that cookie which serves as a remember cookie on
        // the application. Once we have a user we can return it to the caller.
        if (is_null($userBySession) && $userByCookie) {
            // The `updateSession` method changes session ID,
            // and we need old session ID for later usage.
            $oldSession = $this->session->getId();

            // Update user session
            $this->updateSession($userByCookie->getAuthIdentifier());

            // Update user persistence
            $this->updatePersistence($userByCookie->id, $oldSession, false);

            // Fire the authentication login event
            $this->events->fire('rinvex.fort.auth.login', [$userByCookie, true]);
        }

        // Prepare current persistence instance
        $persistence = $this->getPersistenceByToken($this->session->getId());

        // Check if we've a valid persistence
        if (! $this->logoutAttempted && ($userBySession || $userByCookie) && ! $persistence) {
            $this->logout();

            // Fire the automatic logout event
            $this->events->fire('rinvex.fort.auth.autologout');

            // Throw invalid persistence exception
            throw new InvalidPersistenceException();
        }

        // Prepare current user instance
        $user = $userBySession ?: $userByCookie;

        // Update last activity
        if (! $this->logoutAttempted && ! is_null($user) && ! is_null($persistence)) {
            $persistence->touch();
        }

        return $this->user = $user;
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|null
     */
    public function id()
    {
        if ($this->loggedOut) {
            return;
        }

        $id = $this->session->get($this->getName());

        if (is_null($id) && $this->user()) {
            $id = $this->user()->getAuthIdentifier();
        }

        return $id;
    }

    /**
     * Pull a user from the repository by its recaller ID.
     *
     * @param string $recaller
     *
     * @return mixed
     */
    protected function getUserByRecaller($recaller)
    {
        if ($this->validRecaller($recaller) && ! $this->tokenRetrievalAttempted) {
            $this->tokenRetrievalAttempted = true;

            list($id, $token) = explode('|', $recaller, 2);

            $this->viaRemember = ! is_null($user = $this->provider->findByToken($id, $token));

            return $user;
        }
    }

    /**
     * Get the decrypted recaller cookie for the request.
     *
     * @return string|null
     */
    protected function getRecaller()
    {
        return $this->request->cookies->get($this->getRecallerName());
    }

    /**
     * Get the user ID from the recaller cookie.
     *
     * @return string|null
     */
    protected function getRecallerId()
    {
        if ($this->validRecaller($recaller = $this->getRecaller())) {
            return head(explode('|', $recaller));
        }
    }

    /**
     * Determine if the recaller cookie is in a valid format.
     *
     * @param mixed $recaller
     *
     * @return bool
     */
    protected function validRecaller($recaller)
    {
        if (! is_string($recaller) || ! Str::contains($recaller, '|')) {
            return false;
        }

        $segments = explode('|', $recaller);

        return count($segments) == 2 && trim($segments[0]) !== '' && trim($segments[1]) !== '';
    }

    /**
     * Log a user into the application without sessions or cookies.
     *
     * @param array $credentials
     *
     * @return bool
     */
    public function once(array $credentials = [])
    {
        if ($this->validate($credentials)) {
            $this->setUser($this->lastAttempted);

            return true;
        }

        return false;
    }

    /**
     * Validate a user's credentials.
     *
     * @param array $credentials
     *
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return $this->attempt($credentials, false, false);
    }

    /**
     * Attempt to authenticate using HTTP Basic Auth.
     *
     * @param string $field
     * @param array  $extraConditions
     *
     * @return \Illuminate\Http\Response|null
     */
    public function basic($field = 'email', $extraConditions = [])
    {
        if ($this->check()) {
            return;
        }

        // If a username is set on the HTTP basic request, we will return out without
        // interrupting the request lifecycle. Otherwise, we'll need to generate a
        // request indicating that the given credentials were invalid for login.
        if ($this->attemptBasic($this->getRequest(), $field, $extraConditions)) {
            return;
        }

        return $this->getBasicResponse();
    }

    /**
     * Perform a stateless HTTP Basic login attempt.
     *
     * @param string $field
     * @param array  $extraConditions
     *
     * @return \Illuminate\Http\Response|null
     */
    public function onceBasic($field = 'email', $extraConditions = [])
    {
        $credentials = $this->getBasicCredentials($this->getRequest(), $field);

        if (! $this->once(array_merge($credentials, $extraConditions))) {
            return $this->getBasicResponse();
        }
    }

    /**
     * Attempt to authenticate using basic authentication.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $field
     * @param array                    $extraConditions
     *
     * @return bool
     */
    protected function attemptBasic(Request $request, $field, $extraConditions = [])
    {
        if (! $request->getUser()) {
            return false;
        }

        $credentials = $this->getBasicCredentials($request, $field);

        return $this->attempt(array_merge($credentials, $extraConditions));
    }

    /**
     * Get the credential array for a HTTP Basic request.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $field
     *
     * @return array
     */
    protected function getBasicCredentials(Request $request, $field)
    {
        return [
            $field     => $request->getUser(),
            'password' => $request->getPassword(),
        ];
    }

    /**
     * Get the response for basic authentication.
     *
     * @return \Illuminate\Http\Response
     */
    protected function getBasicResponse()
    {
        $headers = ['WWW-Authenticate' => 'Basic'];

        return new Response('Invalid credentials.', 401, $headers);
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param array $credentials
     * @param bool  $remember
     * @param bool  $login
     *
     * @return string
     */
    public function attempt(array $credentials = [], $remember = false, $login = true)
    {
        $credentials = $credentials + ['moderated' => 0];

        // Fire the authentication attempt event
        $this->events->fire('rinvex.fort.auth.attempt', [$credentials, $remember, $login]);

        $this->lastAttempted = $user = $this->provider->findByCredentials($credentials);

        // Login throttling
        $throttles = config('rinvex.fort.throttle.enabled');
        $lockedOut = $this->hasTooManyLoginAttempts($this->getRequest());

        if ($throttles && $lockedOut) {
            // Fire the authentication lockout event (only if user exists)
            if ($user) {
                $this->events->fire('rinvex.fort.auth.lockout', [$this->getRequest()]);
            }

            return static::AUTH_LOCKED_OUT;
        }

        // If an implementation of UserInterface was returned, we'll ask the provider
        // to validate the user against the given credentials, and if they are in
        // fact valid we'll log the users into the application and return true.
        if ($this->hasValidCredentials($user, $credentials)) {
            // Check if unverified
            if (config('rinvex.fort.verification.required') && ! $user->isEmailVerified()) {
                // Fire the authentication unverified event
                $this->events->fire('rinvex.fort.auth.unverified', [$user]);

                // Verification required
                return static::AUTH_UNVERIFIED;
            }

            $totp  = array_get($user->getTwoFactor(), 'totp.enabled');
            $phone = array_get($user->getTwoFactor(), 'phone.enabled');

            // Enforce Two-Factor authentication
            if ($totp || $phone) {
                // Update user persistence
                $this->updatePersistence($user->id, $this->session->getId(), true);

                $this->session->flash('rinvex.fort.twofactor.methods', ['totp' => $totp, 'phone' => $phone]);
                $this->session->flash('rinvex.fort.twofactor.remember', $remember);
                $this->session->flash('rinvex.fort.twofactor.persistence', $this->session->getId());

                // Fire the Two-Factor authentication required event
                $this->events->fire('rinvex.fort.twofactor.required', [$user]);

                return static::AUTH_TWOFACTOR_REQUIRED;
            }

            // If Two-Factor enabled, `attempt` method always returns false,
            // use `login` or `loginUsingId` methods to login users in such case.
            if ($login) {
                return $this->login($user, $remember);
            }

            // Valid credentials, clear login attempts
            if ($throttles) {
                $this->clearLoginAttempts($this->getRequest());
            }

            // Fire the authentication valid event
            $this->events->fire('rinvex.fort.auth.valid', [$credentials, $remember]);

            return static::AUTH_VALID;
        }

        // Invalid credentials, increment login attempts
        if ($throttles && ! $lockedOut) {
            $this->incrementLoginAttempts($this->getRequest());
        }

        // Clear Two-Factor authentication attempts
        $this->clearTwoFactor();

        // Fire the authentication failed event
        $this->events->fire('rinvex.fort.auth.failed', [$credentials, $remember]);

        return static::AUTH_FAILED;
    }

    /**
     * Determine if the user matches the credentials.
     *
     * @param mixed $user
     * @param array $credentials
     *
     * @return bool
     */
    protected function hasValidCredentials($user, $credentials)
    {
        return ! is_null($user) && $this->provider->validateCredentials($user, $credentials);
    }

    /**
     * Log a user into the application.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     * @param bool                                           $remember
     * @param string                                         $persistence
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
            $this->createRememberTokenIfDoesntExist($user);

            $this->queueRecallerCookie($user);
        }

        // Check persistence mode
        if (config('rinvex.fort.persistence') === 'single') {
            $user->persistences()->delete();
        }

        // Update user last login datetime
        $this->provider->update($user, ['login_at' => new Carbon()]);

        // Update user persistence
        $this->updatePersistence($user->id, $persistence ?: $this->session->getId(), false);

        // Login successful, clear login attempts
        if (config('rinvex.fort.throttle.enabled')) {
            $this->clearLoginAttempts($this->getRequest());
        }

        // Clear Two-Factor authentication attempts
        $this->clearTwoFactor();

        // Fire the authentication login event
        $this->events->fire('rinvex.fort.auth.login', [$user, $remember]);

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
        $this->session->set($this->getName(), $id);

        $this->session->migrate(true);
    }

    /**
     * Log the given user ID into the application.
     *
     * @param mixed $id
     * @param bool  $remember
     *
     * @return \Rinvex\Fort\Contracts\AuthenticatableContract|false
     */
    public function loginUsingId($id, $remember = false)
    {
        $user = $this->provider->find($id);

        if (! is_null($user)) {
            $this->login($user, $remember);

            return $user;
        }

        return false;
    }

    /**
     * Log the given user ID into the application without sessions or cookies.
     *
     * @param mixed $id
     *
     * @return \Rinvex\Fort\Contracts\AuthenticatableContract|false
     */
    public function onceUsingId($id)
    {
        $user = $this->provider->find($id);

        if (! is_null($user)) {
            $this->setUser($user);

            return $user;
        }

        return false;
    }

    /**
     * Queue the recaller cookie into the cookie jar.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     *
     * @return void
     */
    protected function queueRecallerCookie(AuthenticatableContract $user)
    {
        $value = $user->getAuthIdentifier().'|'.$user->getRememberToken();

        $this->getCookieJar()->queue($this->createRecaller($value));
    }

    /**
     * Create a "remember me" cookie for a given ID.
     *
     * @param string $value
     *
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    protected function createRecaller($value)
    {
        return $this->getCookieJar()->forever($this->getRecallerName(), $value);
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
            $this->refreshRememberToken($user);
        }

        // Delete user persistence
        app('rinvex.fort.persistence')->delete($this->session->getId());

        // Fire the authentication logout event
        $this->events->fire('rinvex.fort.auth.logout', [$user]);

        // Once we have fired the logout event we will clear the users out of memory
        // so they are no longer available as the user is no longer considered as
        // being signed into this application and should not be available here.
        $this->user = null;

        $this->loggedOut = true;

        return static::AUTH_LOGOUT;
    }

    /**
     * Remove the user data from the session and cookies.
     *
     * @return void
     */
    protected function clearUserDataFromStorage()
    {
        $this->session->remove($this->getName());

        if (! is_null($this->getRecaller())) {
            $recaller = $this->getRecallerName();

            $this->getCookieJar()->queue($this->getCookieJar()->forget($recaller));
        }
    }

    /**
     * Refresh the "remember me" token for the user.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     *
     * @return void
     */
    protected function refreshRememberToken(AuthenticatableContract $user)
    {
        $user->setRememberToken($token = Str::random(60));

        $this->provider->updateRememberToken($user, $token);
    }

    /**
     * Create a new "remember me" token for the user if one doesn't already exist.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     *
     * @return void
     */
    protected function createRememberTokenIfDoesntExist(AuthenticatableContract $user)
    {
        if (empty($user->getRememberToken())) {
            $this->refreshRememberToken($user);
        }
    }

    /**
     * Get the cookie creator instance used by the guard.
     *
     * @throws \RuntimeException
     *
     * @return \Illuminate\Contracts\Cookie\QueueingFactory
     */
    public function getCookieJar()
    {
        if (! isset($this->cookie)) {
            throw new RuntimeException('Cookie jar has not been set.');
        }

        return $this->cookie;
    }

    /**
     * Set the cookie creator instance used by the guard.
     *
     * @param \Illuminate\Contracts\Cookie\QueueingFactory $cookie
     *
     * @return void
     */
    public function setCookieJar(CookieJar $cookie)
    {
        $this->cookie = $cookie;
    }

    /**
     * Get the event dispatcher instance.
     *
     * @return \Illuminate\Contracts\Events\Dispatcher
     */
    public function getDispatcher()
    {
        return $this->events;
    }

    /**
     * Set the event dispatcher instance.
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     *
     * @return void
     */
    public function setDispatcher(Dispatcher $events)
    {
        $this->events = $events;
    }

    /**
     * Get the session store used by the guard.
     *
     * @return \Illuminate\Session\Store
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Get the user provider used by the guard.
     *
     * @return \Rinvex\Fort\Contracts\UserRepositoryContract
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Set the user provider used by the guard.
     *
     * @param \Rinvex\Fort\Contracts\UserRepositoryContract $provider
     *
     * @return void
     */
    public function setProvider(UserRepositoryContract $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Return the currently cached user.
     *
     * @return \Rinvex\Fort\Contracts\AuthenticatableContract|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the current user.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     *
     * @return void
     */
    public function setUser(AuthenticatableContract $user)
    {
        $this->user = $user;

        $this->loggedOut = false;

        // Fire the authenticated event
        $this->events->fire('rinvex.fort.auth.user', [$user]);

        return $this;
    }

    /**
     * Get the current request instance.
     *
     * @return \Illuminate\Http\Request
     */
    public function getRequest()
    {
        return $this->request ?: Request::capture();
    }

    /**
     * Set the current request instance.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Get the last user we attempted to authenticate.
     *
     * @return \Rinvex\Fort\Contracts\AuthenticatableContract
     */
    public function getLastAttempted()
    {
        return $this->lastAttempted;
    }

    /**
     * Get a unique identifier for the auth session value.
     *
     * @return string
     */
    public function getName()
    {
        return 'login_'.$this->name.'_'.sha1(static::class);
    }

    /**
     * Get the name of the cookie used to store the "recaller".
     *
     * @return string
     */
    public function getRecallerName()
    {
        return 'remember_'.$this->name.'_'.sha1(static::class);
    }

    /**
     * Determine if the user was authenticated via "remember me" cookie.
     *
     * @return bool
     */
    public function viaRemember()
    {
        return $this->viaRemember;
    }

    /**
     * Clear Two-Factor authentication attempts.
     *
     * @return void
     */
    protected function clearTwoFactor()
    {
        $this->session->forget('rinvex.fort.twofactor.methods');
        $this->session->forget('rinvex.fort.twofactor.remember');
        $this->session->forget('rinvex.fort.twofactor.persistence');
    }

    /**
     * Remember Two-Factor authentication attempts.
     *
     * @return void
     */
    public function rememberTwoFactor()
    {
        $this->session->keep([
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
        $ip    = request()->ip();

        // Delete previous user persistence
        app('rinvex.fort.persistence')->delete($token);

        // Create new user persistence
        app('rinvex.fort.persistence')->create([
            'user_id'    => $id,
            'token'      => $this->session->getId(),
            'attempt'    => $attempt,
            'agent'      => $agent,
            'ip'         => $ip,
        ]);
    }

    /**
     * Verify Two-Factor authentication.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     * @param string                                         $token
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
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     * @param                                                $token
     *
     * @return void
     */
    protected function invalidateTwoFactorBackup(AuthenticatableContract $user, $token)
    {
        $settings = $user->getTwoFactor();
        $backup   = array_get($settings, 'totp.backup');

        unset($backup[array_search($token, $backup)]);

        array_set($settings, 'totp.backup', $backup);

        // Update Two-Factor OTP backup codes
        $this->provider->update($user, [
            'two_factor' => $settings,
        ]);
    }

    /**
     * Determine if the given token is a valid Two-Factor Phone token.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     * @param                                                $token
     *
     * @return bool
     */
    protected function isValidTwoFactorPhone(AuthenticatableContract $user, $token)
    {
        $authy = app(TwoFactorAuthyProvider::class);

        return strlen($token) === 7 && isset($this->session->get('rinvex.fort.twofactor.methods')['phone']) && $authy->tokenIsValid($user, $token);
    }

    /**
     * Determine if the given token is a valid Two-Factor Backup code.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     * @param                                                $token
     *
     * @return bool
     */
    protected function isValidTwoFactorBackup(AuthenticatableContract $user, $token)
    {
        // Fire the Two-Factor TOTP backup verify start event
        $this->events->fire('rinvex.fort.twofactor.backup.verify.start', [$user, $token]);

        $backup = array_get($user->getTwoFactor(), 'totp.backup', []);
        $result = strlen($token) === 10 && in_array($token, $backup);

        if ($result) {
            // Fire the Two-Factor TOTP backup verify success event
            $this->events->fire('rinvex.fort.twofactor.backup.verify.success', [$user, $token]);
        } else {
            // Fire the Two-Factor TOTP backup verify failed event
            $this->events->fire('rinvex.fort.twofactor.backup.verify.failed', [$user, $token]);
        }

        return $result;
    }

    /**
     * Determine if the given token is a valid Two-Factor TOTP token.
     *
     * @param \Rinvex\Fort\Contracts\AuthenticatableContract $user
     * @param                                                $token
     *
     * @return bool
     */
    protected function isValidTwoFactorTotp(AuthenticatableContract $user, $token)
    {
        $totp   = app(TwoFactorTotpProvider::class);
        $secret = array_get($user->getTwoFactor(), 'totp.secret');

        return strlen($token) === 6 && isset($this->session->get('rinvex.fort.twofactor.methods')['totp']) && $totp->verifyKey($secret, $token);
    }

    /**
     * Register a new user.
     *
     * @param array $credentials
     *
     * @return string
     */
    public function register(array $credentials)
    {
        // Fire the register start event
        $this->events->fire('rinvex.fort.register.start', [$credentials]);

        // Prepare registration data
        $credentials['password']  = bcrypt($credentials['password']);
        $credentials['moderated'] = config('rinvex.fort.registration.moderated');

        // Create new user
        $user = $this->provider->create($credentials);

        // Fire the register success event
        $this->events->fire('rinvex.fort.register.success', [$user]);

        // Send verification if required
        if (config('rinvex.fort.verification.required')) {
            return app('rinvex.fort.verifier')->broker()->sendVerificationLink(['email' => $credentials['email']]);
        }

        // Registration completed successfully
        return static::AUTH_REGISTERED;
    }

    /**
     * Register a new social user.
     *
     * @param array $credentials
     *
     * @return string
     */
    public function registerSocialite(array $credentials)
    {
        // Fire the register social start event
        $this->events->fire('rinvex.fort.register.social.start', [$credentials]);

        // Prepare registration data
        $credentials['password']  = bcrypt(str_random());
        $credentials['moderated'] = config('rinvex.fort.registration.moderated');

        // Create new user
        $user = $this->provider->create($credentials);

        // Fire the register social success event
        $this->events->fire('rinvex.fort.register.social.success', [$user]);

        // Registration completed successfully
        return $user;
    }

    /**
     * Pull a persistence from the repository by its token.
     *
     * @return \Rinvex\Fort\Repositories\PersistenceRepository
     */
    public function getPersistenceByToken($token)
    {
        return app('rinvex.fort.persistence')->findByToken($token);
    }

    /**
     * Pull a user from the repository by its session ID.
     *
     * @return \Rinvex\Fort\Contracts\AuthenticatableContract|null
     */
    protected function getUserBySession()
    {
        return ! is_null($id = $this->session->get($this->getName())) ? $this->provider->find($id) : null;
    }

    /**
     * Pull a user from the repository by its cookie remember me ID.
     *
     * @return \Rinvex\Fort\Contracts\AuthenticatableContract|null
     */
    protected function getUserByCookie()
    {
        return ! is_null($recaller = $this->getRecaller()) ? $this->getUserByRecaller($recaller) : null;
    }
}
