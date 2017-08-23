<?php

declare(strict_types=1);

namespace Rinvex\Fort\Policies;

use Rinvex\Fort\Contracts\UserContract;
use Illuminate\Auth\Access\HandlesAuthorization;

class DashboardPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can access the dashboard.
     *
     * @param string                              $ability
     * @param \Rinvex\Fort\Contracts\UserContract $user
     *
     * @return bool
     */
    public function access($ability, UserContract $user)
    {
        return $user->allAbilities->pluck('slug')->contains($ability);
    }
}
