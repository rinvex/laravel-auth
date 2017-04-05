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

declare(strict_types=1);

namespace Rinvex\Fort\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ResetTimezone
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (($user = Auth::guard($guard)->user()) && $user->timezone) {
            config(['app.timezone' => $user->timezone]);
            date_default_timezone_set($user->timezone);
        }

        return $next($request);
    }
}
