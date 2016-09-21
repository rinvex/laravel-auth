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
use Rinvex\Fort\Models\Ability;
use Illuminate\Auth\Access\HandlesAuthorization;

class AbilityPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the ability.
     *
     * @param \Rinvex\Fort\Models\User    $user
     * @param \Rinvex\Fort\Models\Ability $ability
     *
     * @return bool
     */
    public function view(User $user, Ability $ability)
    {
        return true;
    }

    /**
     * Determine whether the user can create abilities.
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
     * Determine whether the user can update the ability.
     *
     * @param \Rinvex\Fort\Models\User    $user
     * @param \Rinvex\Fort\Models\Ability $ability
     *
     * @return bool
     */
    public function update(User $user, Ability $ability)
    {
        // Super admin & protected abilities can be controlled by super admins only!
        return $ability->isProtected() || ($ability->isSuperadmin() && ! $user->isSuperadmin()) ? false : true;
    }

    /**
     * Determine whether the user can delete the ability.
     *
     * @param \Rinvex\Fort\Models\User    $user
     * @param \Rinvex\Fort\Models\Ability $ability
     *
     * @return bool
     */
    public function delete(User $user, Ability $ability)
    {
        // Super admin & protected abilities can be controlled by super admins only! Deleted ability must have no roles or users attached!!
        return $ability->isProtected() || ($ability->isSuperadmin() && ! $user->isSuperadmin()) || ! $ability->roles->isEmpty() || ! $ability->users->isEmpty() ? false : true;
    }

    /**
     * Determine whether the user can import the abilities.
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
     * Determine whether the user can export the abilities.
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
     * Determine whether the user can assign the ability.
     *
     * @param \Rinvex\Fort\Models\User $user
     *
     * @return bool
     */
    public function assign(User $user)
    {
        // Regardless of this ability, admins must have
        // the assigned abilities before assigning it to others.
        return true;
    }

    /**
     * Determine whether the user can revoke the ability.
     *
     * @param \Rinvex\Fort\Models\User $user
     *
     * @return bool
     */
    public function revoke(User $user)
    {
        // Regardless of this ability, admins must have
        // the revoked abilities before revoking it from others.
        return true;
    }
}
