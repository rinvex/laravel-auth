<?php

declare(strict_types=1);

namespace Rinvex\Fort\Policies;

use Rinvex\Fort\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminareaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can access the adminarea.
     *
     * @param string                              $ability
     * @param \Rinvex\Fort\Models\User $user
     *
     * @return bool
     */
    public function access($ability, User $user): bool
    {
        return $user->allAbilities->pluck('slug')->contains($ability);
    }
}
