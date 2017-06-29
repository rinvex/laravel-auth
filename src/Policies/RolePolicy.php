<?php

declare(strict_types=1);

namespace Rinvex\Fort\Policies;

use Rinvex\Fort\Models\Role;
use Rinvex\Fort\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can list roles.
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
     * Determine whether the user can create roles.
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
     * Determine whether the user can update the role.
     *
     * @param string                   $ability
     * @param \Rinvex\Fort\Models\User $user
     * @param \Rinvex\Fort\Models\Role $resource
     *
     * @return bool
     */
    public function update($ability, User $user, Role $resource)
    {
        return $user->allAbilities->pluck('slug')->contains($ability)           // User can update roles
               && $user->hasRole($resource)                                     // User already have RESOURCE role
               && ! $resource->isSuperadmin()                                   // RESOURCE role is NOT superadmin
               && ! $resource->isProtected();                                   // RESOURCE role is NOT protected
    }

    /**
     * Determine whether the user can delete the role.
     *
     * @param string                   $ability
     * @param \Rinvex\Fort\Models\User $user
     * @param \Rinvex\Fort\Models\Role $resource
     *
     * @return bool
     */
    public function delete($ability, User $user, Role $resource)
    {
        return $resource->abilities->isEmpty()                                  // RESOURCE role has no abilities attached
               && $resource->users->isEmpty()                                   // RESOURCE role has no users attached
               && $user->allAbilities->pluck('slug')->contains($ability)        // User can delete roles
               && $user->allAbilities->pluck('slug')->contains($resource->slug) // User already have RESOURCE role
               && ! $resource->isSuperadmin()                                   // RESOURCE role is NOT superadmin
               && ! $resource->isProtected();                                   // RESOURCE role is NOT protected
    }

    /**
     * Determine whether the user can assign the given role to the given user.
     *
     * @param string                   $ability
     * @param \Rinvex\Fort\Models\User $user
     * @param \Rinvex\Fort\Models\Role $resource
     * @param \Rinvex\Fort\Models\User $resourced
     *
     * @return bool
     */
    public function assign($ability, User $user, Role $resource, User $resourced)
    {
        return $user->allAbilities->pluck('slug')->contains($ability)           // User can assign roles
               && $user->allAbilities->pluck('slug')->contains($resource->slug) // User already have RESOURCE role
               && ! $resourced->isSuperadmin()                                  // RESOURCED user is NOT superadmin
               && ! $resourced->isProtected();                                  // RESOURCED user is NOT protected
    }
}
