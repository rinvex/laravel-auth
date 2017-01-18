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
use Rinvex\Fort\Models\User;
use Rinvex\Fort\Models\Ability;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Model;

class Abilities
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Bypass authorization check if superadmin
        Gate::before(function (User $user) {
            return $user->isSuperadmin() ?: null;
        });

        // Define abilities and policies
        Ability::all()->map(function ($ability) {
            Gate::define($ability->slug, $ability->policy ?: function (User $user, Model $resource = null) use ($ability) {
                return $user->allAbilities->pluck('slug')->contains($ability->slug);
            });
        });

        return $next($request);
    }
}
