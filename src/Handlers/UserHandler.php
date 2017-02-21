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
     * Listen to the User validated event.
     *
     * @param \Rinvex\Fort\Models\User $user
     * @param string                   $event
     *
     * @return void
     */
    public function validated(User $user, $event)
    {
        // Auto hash password after validation passed/skipped
        // Hashing in boot method rather than mutators is required
        // to use raw value in validation, otherwise mutated value used
        if ($user->isDirty('password') && in_array($event, ['passed', 'skipped'])) {
            $user->password = bcrypt($user->password);
        }
    }
}
