<?php

declare(strict_types=1);

namespace Rinvex\Fort\Policies;

use Rinvex\Fort\Models\User;
use Rinvex\Fort\Models\Ability;
use Illuminate\Auth\Access\HandlesAuthorization;

class AbilityPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can list abilities.
     *
     * @param string                   $ability
     * @param \Rinvex\Fort\Models\User $user
     *
     * @return bool
     */
    public function list($ability, User $user)
    {
        return $user->allAbilities->pluck('slug')->contains($ability);
    }

    /**
     * Determine whether the user can create abilities.
     *
     * @param string                   $ability
     * @param \Rinvex\Fort\Models\User $user
     *
     * @return bool
     */
    public function create($ability, User $user)
    {
        return $user->allAbilities->pluck('slug')->contains($ability);
    }

    /**
     * Determine whether the user can update the ability.
     *
     * @param string                      $ability
     * @param \Rinvex\Fort\Models\User    $user
     * @param \Rinvex\Fort\Models\Ability $resource
     *
     * @return bool
     */
    public function update($ability, User $user, Ability $resource)
    {
        return $user->allAbilities->pluck('slug')->contains($ability)           // User can update abilities
               && $user->allAbilities->pluck('slug')->contains($resource->slug) // User already have RESOURCE ability
               && ! $resource->isSuperadmin()                                   // RESOURCE ability is NOT superadmin
               && ! $resource->isProtected();                                   // RESOURCE ability is NOT protected
    }

    /**
     * Determine whether the user can delete the ability.
     *
     * @param string                      $ability
     * @param \Rinvex\Fort\Models\User    $user
     * @param \Rinvex\Fort\Models\Ability $resource
     *
     * @return bool
     */
    public function delete($ability, User $user, Ability $resource)
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
     * @param string                      $ability
     * @param \Rinvex\Fort\Models\User    $user
     * @param \Rinvex\Fort\Models\Ability $resource
     * @param \Rinvex\Fort\Models\User    $resourced
     *
     * @return bool
     */
    public function grant($ability, User $user, Ability $resource, User $resourced)
    {
        return $user->allAbilities->pluck('slug')->contains($ability)           // User can grant abilities
               && $user->allAbilities->pluck('slug')->contains($resource->slug) // User already have RESOURCE ability
               && ! $resourced->isSuperadmin()                                  // RESOURCED user is NOT superadmin
               && ! $resourced->isProtected();                                  // RESOURCED user is NOT protected
    }
}
