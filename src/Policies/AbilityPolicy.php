<?php

declare(strict_types=1);

namespace Rinvex\Fort\Policies;

use Rinvex\Fort\Contracts\UserContract;
use Rinvex\Fort\Contracts\AbilityContract;
use Illuminate\Auth\Access\HandlesAuthorization;

class AbilityPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can list abilities.
     *
     * @param string                              $ability
     * @param \Rinvex\Fort\Contracts\UserContract $user
     *
     * @return bool
     */
    public function list($ability, UserContract $user)
    {
        return $user->allAbilities->pluck('slug')->contains($ability);
    }

    /**
     * Determine whether the user can create abilities.
     *
     * @param string                              $ability
     * @param \Rinvex\Fort\Contracts\UserContract $user
     *
     * @return bool
     */
    public function create($ability, UserContract $user)
    {
        return $user->allAbilities->pluck('slug')->contains($ability);
    }

    /**
     * Determine whether the user can update the ability.
     *
     * @param string                                 $ability
     * @param \Rinvex\Fort\Contracts\UserContract    $user
     * @param \Rinvex\Fort\Contracts\AbilityContract $resource
     *
     * @return bool
     */
    public function update($ability, UserContract $user, AbilityContract $resource)
    {
        return $user->allAbilities->pluck('slug')->contains($ability)           // User can update abilities
               && $user->allAbilities->pluck('slug')->contains($resource->slug) // User already have RESOURCE ability
               && ! $resource->isSuperadmin()                                   // RESOURCE ability is NOT superadmin
               && ! $resource->isProtected();                                   // RESOURCE ability is NOT protected
    }

    /**
     * Determine whether the user can delete the ability.
     *
     * @param string                                 $ability
     * @param \Rinvex\Fort\Contracts\UserContract    $user
     * @param \Rinvex\Fort\Contracts\AbilityContract $resource
     *
     * @return bool
     */
    public function delete($ability, UserContract $user, AbilityContract $resource)
    {
        return $resource->roles->isEmpty()                                      // RESOURCE ability has no roles attached
               && $resource->users->isEmpty()                                   // RESOURCE ability has no users attached
               && $user->allAbilities->pluck('slug')->contains($ability)        // User can delete abilities
               && $user->allAbilities->pluck('slug')->contains($resource->slug) // User already have RESOURCE ability
               && ! $resource->isSuperadmin()                                   // RESOURCE ability is NOT superadmin
               && ! $resource->isProtected();                                   // RESOURCE ability is NOT protected
    }

    /**
     * Determine whether the user can grant the given ability to the given user.
     *
     * @param string                                 $ability
     * @param \Rinvex\Fort\Contracts\UserContract    $user
     * @param \Rinvex\Fort\Contracts\AbilityContract $resource
     * @param \Rinvex\Fort\Contracts\UserContract    $resourced
     *
     * @return bool
     */
    public function grant($ability, UserContract $user, AbilityContract $resource, UserContract $resourced)
    {
        return $user->allAbilities->pluck('slug')->contains($ability)           // User can grant abilities
               && $user->allAbilities->pluck('slug')->contains($resource->slug) // User already have RESOURCE ability
               && ! $resourced->isSuperadmin()                                  // RESOURCED user is NOT superadmin
               && ! $resourced->isProtected();                                  // RESOURCED user is NOT protected
    }
}
