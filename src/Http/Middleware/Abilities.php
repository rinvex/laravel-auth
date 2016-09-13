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
        if (! ($id = Auth::guard($guard)->id()) || ! $this->hasAbilityTo($id, $request->route()->getName())) {
            // Fire the unauthorized event
            event('rinvex.fort.auth.unauthorized');

            return ! $id ? intend([
                'intended'   => route('rinvex.fort.frontend.auth.login'),
                'withErrors' => ['rinvex.fort.session.expired' => Lang::get('rinvex.fort::message.auth.session.required')],
            ], 401) : intend([
                'intended'   => route('home'),
                'withErrors' => ['no_ability' => Lang::get('rinvex.fort::message.auth.unauthorized')],
            ], 401);
        }

        return $next($request);
    }

    /**
     * Determine if user has ability to access this route.
     *
     * @param int    $id
     * @param string $routeName
     *
     * @return bool
     */
    protected function hasAbilityTo($id, $routeName)
    {
        $user = app('rinvex.fort.user')->with(['abilities', 'roles'])->find($id);

        return app('rinvex.fort.user')->hasAbilityTo($user, ['global.superuser', $routeName]);
    }
}
