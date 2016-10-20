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

namespace Rinvex\Fort\Policies;

use Rinvex\Fort\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DashboardPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can access the dashboard.
     *
     * @param string                   $ability
     * @param \Rinvex\Fort\Models\User $user
     *
     * @return bool
     */
    public function access($ability, User $user)
    {
        return $user->allAbilities->pluck('slug')->contains($ability);
    }
}
