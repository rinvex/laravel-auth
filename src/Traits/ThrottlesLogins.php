<?php

declare(strict_types=1);

namespace Rinvex\Fort\Traits;

use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Foundation\Auth\ThrottlesLogins as BaseThrottlesLogins;

trait ThrottlesLogins
{
    use BaseThrottlesLogins;

    /**
     * Determine if the user has too many failed login attempts.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return bool
     */
    protected function hasTooManyLoginAttempts(Request $request)
    {
        $throttleKey = $this->throttleKey($request);
        $throttleAttempts = config('rinvex.fort.throttle.max_login_attempts', 5);
        $throttleTimeout = config('rinvex.fort.throttle.lockout_time', 1);

        return $this->limiter()->tooManyAttempts($throttleKey, $throttleAttempts, $throttleTimeout);
    }

    /**
     * Get the throttle key for the given request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return string
     */
    protected function throttleKey(Request $request)
    {
        return Str::lower($request->input('loginfield')).'|'.$request->ip();
    }

    /**
     * Get the lockout seconds.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return int
     */
    public function secondsRemainingOnLockout(Request $request)
    {
        return $this->limiter()->availableIn($this->throttleKey($request));
    }
}
