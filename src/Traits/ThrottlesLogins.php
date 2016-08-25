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

use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;

trait ThrottlesLogins
{
    /**
     * Get the lockout seconds.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return int
     */
    public function secondsRemainingOnLockout(Request $request)
    {
        return app(RateLimiter::class)->availableIn($this->getThrottleKey($request));
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
        app(RateLimiter::class)->clear($this->getThrottleKey($request));
    }

    /**
     * Get the throttle key for the given request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    protected function getThrottleKey(Request $request)
    {
        return mb_strtolower($request->input('loginfield')).'|'.$request->ip();
    }

    /**
     * Determine if the user has too many failed login attempts.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function hasTooManyLoginAttempts(Request $request)
    {
        $throttleKey      = $this->getThrottleKey($request);
        $throttleAttempts = config('rinvex.fort.throttle.maxloginattempts', 5);
        $throttleTimeout  = config('rinvex.fort.throttle.lockouttime', 60) / 60;

        return app(RateLimiter::class)->tooManyAttempts($throttleKey, $throttleAttempts, $throttleTimeout);
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
        app(RateLimiter::class)->hit($this->getThrottleKey($request));
    }

    /**
     * Determine how many retries are left for the user.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return int
     */
    protected function retriesLeft(Request $request)
    {
        return app(RateLimiter::class)->retriesLeft($this->getThrottleKey($request), config('rinvex.fort.throttle.maxloginattempts', 5));
    }
}
