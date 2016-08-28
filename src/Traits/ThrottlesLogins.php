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

namespace Rinvex\Fort\Traits;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;

trait ThrottlesLogins
{
    /**
     * Determine if the user has too many failed login attempts.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function hasTooManyLoginAttempts(Request $request)
    {
        $throttleKey      = $this->throttleKey($request);
        $throttleAttempts = config('rinvex.fort.throttle.max_login_attempts', 5);
        $throttleTimeout  = config('rinvex.fort.throttle.lockout_time', 1);

        return $this->limiter()->tooManyAttempts($throttleKey, $throttleAttempts, $throttleTimeout);
    }

    /**
     * Increment the login attempts for the user.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return int
     */
    protected function incrementLoginAttempts(Request $request)
    {
        $this->limiter()->hit($this->throttleKey($request));
    }

    /**
     * Clear the login locks for the given user credentials.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    protected function clearLoginAttempts(Request $request)
    {
        $this->limiter()->clear($this->throttleKey($request));
    }

    /**
     * Get the throttle key for the given request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    protected function throttleKey(Request $request)
    {
        return Str::lower($request->input('loginfield')).'|'.$request->ip();
    }

    /**
     * Get the rate limiter instance.
     *
     * @return \Illuminate\Cache\RateLimiter
     */
    protected function limiter()
    {
        return app(RateLimiter::class);
    }

    /**
     * Get the lockout seconds.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return int
     */
    public function secondsRemainingOnLockout(Request $request)
    {
        return $this->limiter()->availableIn($this->throttleKey($request));
    }
}
