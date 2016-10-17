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

namespace Rinvex\Fort\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class Abilities
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param string|null              $guard
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if ($user = Auth::guard($guard)->user()) {
            $user->allAbilities->map(function ($ability) {
                // Bypass authorization if user is super admin already
                Gate::before(function ($user) {
                    return $user->isSuperadmin() ? true : null;
                });

                // Define abilities and policies
                Gate::define($ability->slug, $ability->policy ?: function () {
                    return true;
                });
            });
        }

        return $next($request);
    }
}
