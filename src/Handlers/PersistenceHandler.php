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
use Rinvex\Fort\Models\Persistence;

class PersistenceHandler
{
    /**
     * Listen to the Persistence created event.
     *
     * @param \Rinvex\Fort\Models\Persistence $persistence
     *
     * @return void
     */
    public function created(Persistence $persistence)
    {
        User::forgetCache();
    }

    /**
     * Listen to the Persistence updated event.
     *
     * @param \Rinvex\Fort\Models\Persistence $persistence
     *
     * @return void
     */
    public function updated(Persistence $persistence)
    {
        User::forgetCache();
    }

    /**
     * Listen to the Persistence deleted event.
     *
     * @param \Rinvex\Fort\Models\Persistence $persistence
     *
     * @return void
     */
    public function deleted(Persistence $persistence)
    {
        User::forgetCache();
    }
}
