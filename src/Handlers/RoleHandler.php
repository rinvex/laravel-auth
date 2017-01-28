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

namespace Rinvex\Fort\Handlers;

use Rinvex\Fort\Models\Role;
use Rinvex\Fort\Models\User;
use Rinvex\Fort\Models\Ability;

class RoleHandler
{
    /**
     * Listen to the Role created event.
     *
     * @param \Rinvex\Fort\Models\Role $role
     *
     * @return void
     */
    public function created(Role $role)
    {
        //
    }

    /**
     * Listen to the Role updated event.
     *
     * @param \Rinvex\Fort\Models\Role $role
     *
     * @return void
     */
    public function updated(Role $role)
    {
        Ability::forgetCache();
        User::forgetCache();
    }

    /**
     * Listen to the Role deleted event.
     *
     * @param \Rinvex\Fort\Models\Role $role
     *
     * @return void
     */
    public function deleted(Role $role)
    {
        Ability::forgetCache();
        User::forgetCache();
    }

    /**
     * Listen to the Role attached event.
     *
     * @param \Rinvex\Fort\Models\Role $role
     *
     * @return void
     */
    public function attached(Role $role)
    {
        Ability::forgetCache();
        User::forgetCache();
    }

    /**
     * Listen to the Role synced event.
     *
     * @param \Rinvex\Fort\Models\Role $role
     *
     * @return void
     */
    public function synced(Role $role)
    {
        Ability::forgetCache();
        User::forgetCache();
    }

    /**
     * Listen to the Role detached event.
     *
     * @param \Rinvex\Fort\Models\Role $role
     *
     * @return void
     */
    public function detached(Role $role)
    {
        Ability::forgetCache();
        User::forgetCache();
    }
}
