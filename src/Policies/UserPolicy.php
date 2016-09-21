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

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the user.
     *
     * @param \Rinvex\Fort\Models\User $user
     * @param \Rinvex\Fort\Models\User $model
     *
     * @return bool
     */
    public function view(User $user, User $model)
    {
        return true;
    }

    /**
     * Determine whether the user can create users.
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
     * Determine whether the user can update the user.
     *
     * @param \Rinvex\Fort\Models\User $user
     * @param \Rinvex\Fort\Models\User $model
     *
     * @return bool
     */
    public function update(User $user, User $model)
    {
        // Super admins & protected users can be controlled by super admins only!
        return $model->isProtected() || ($model->isSuperadmin() && ! $user->isSuperadmin()) ? false : true;
    }

    /**
     * Determine whether the user can delete the user.
     *
     * @param \Rinvex\Fort\Models\User $user
     * @param \Rinvex\Fort\Models\User $model
     *
     * @return bool
     */
    public function delete(User $user, User $model)
    {
        // Super admins & protected users can be controlled by super admins only! Users can NOT delete themeselves!
        return $model->isProtected() || ($model->isSuperadmin() && ! $user->isSuperadmin()) || $model->id === $user->id ? false : true;
    }

    /**
     * Determine whether the user can import the users.
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
     * Determine whether the user can export the users.
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
     * Determine whether the user can activate the user.
     *
     * @param \Rinvex\Fort\Models\User    $user
     * @param \Rinvex\Fort\Models\Ability $model
     *
     * @return bool
     */
    public function activate(User $user, User $model)
    {
        // Super admins & protected users can be activated by super
        // admins only! Users can NOT activate their own accounts!
        return $model->isProtected() || ($model->isSuperadmin() && ! $user->isSuperadmin()) || $model->id === $user->id ? false : true;
    }

    /**
     * Determine whether the user can deactivate the user.
     *
     * @param \Rinvex\Fort\Models\User    $user
     * @param \Rinvex\Fort\Models\Ability $model
     *
     * @return bool
     */
    public function deactivate(User $user, User $model)
    {
        // Super admins & protected users can be de-activated by super
        // admins only! Users can NOT de-activate their own accounts!
        return $model->isProtected() || ($model->isSuperadmin() && ! $user->isSuperadmin()) || $model->id === $user->id ? false : true;
    }
}
