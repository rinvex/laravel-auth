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

use Rinvex\Fort\Models\Role;
use Rinvex\Fort\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the role.
     *
     * @param \Rinvex\Fort\Models\User $user
     * @param \Rinvex\Fort\Models\Role $role
     *
     * @return bool
     */
    public function view(User $user, Role $role)
    {
        return true;
    }

    /**
     * Determine whether the user can create roles.
     *
     * @param \Rinvex\Fort\Models\User $user
     *
     * @return bool
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the role.
     *
     * @param \Rinvex\Fort\Models\User $user
     * @param \Rinvex\Fort\Models\Role $role
     *
     * @return bool
     */
    public function update(User $user, Role $role)
    {
        // Super admin & protected roles can be controlled by super admins only!
        return $role->isProtected() || ($role->isSuperadmin() && ! $user->isSuperadmin()) ? false : true;
    }

    /**
     * Determine whether the user can delete the role.
     *
     * @param \Rinvex\Fort\Models\User $user
     * @param \Rinvex\Fort\Models\Role $role
     *
     * @return bool
     */
    public function delete(User $user, Role $role)
    {
        // Super admin & protected roles can be controlled by super admins only! Deleted role must have no abilities or users attached!!
        return $role->isProtected() || ($role->isSuperadmin() && ! $user->isSuperadmin()) || ! $role->abilities->isEmpty() || ! $role->users->isEmpty() ? false : true;
    }

    /**
     * Determine whether the user can import the roles.
     *
     * @param \Rinvex\Fort\Models\User $user
     *
     * @return bool
     */
    public function import(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can export the roles.
     *
     * @param \Rinvex\Fort\Models\User $user
     *
     * @return bool
     */
    public function export(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can give the role.
     *
     * @param \Rinvex\Fort\Models\User $user
     *
     * @return bool
     */
    public function give(User $user)
    {
        // Regardless of this ability, admins must have
        // the given roles before giving it to others.
        return true;
    }

    /**
     * Determine whether the user can remove the role.
     *
     * @param \Rinvex\Fort\Models\User $user
     *
     * @return bool
     */
    public function remove(User $user)
    {
        // Regardless of this ability, admins must have
        // the removed roles before removing it from others.
        return true;
    }
}
