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
use Illuminate\Support\Facades\Lang;

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
        // Check if the user has ability
        if (! Auth::guard($guard)->check() || ! Auth::guard($guard)->user()->hasAbilityTo(['global.superuser', $request->route()->getName()])) {
            // Fire the unauthorized event
            event('rinvex.fort.auth.unauthorized');

            return intend([
                'intended'   => route('home'),
                'withErrors' => ['no_ability' => Lang::get('rinvex.fort::message.auth.unauthorized')],
            ]);
        }

        return $next($request);
    }
}
