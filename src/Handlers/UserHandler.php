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

use Rinvex\Fort\Models\User;

class UserHandler
{
    /**
     * Listen to the User created event.
     *
     * @param \Rinvex\Fort\Models\User $user
     *
     * @return void
     */
    public function created(User $user)
    {
        //
    }

    /**
     * Listen to the User updated event.
     *
     * @param \Rinvex\Fort\Models\User $user
     *
     * @return void
     */
    public function updated(User $user)
    {
        //
    }

    /**
     * Listen to the User deleted event.
     *
     * @param \Rinvex\Fort\Models\User $user
     *
     * @return void
     */
    public function deleted(User $user)
    {
        //
    }
}
