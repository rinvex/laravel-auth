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

namespace Rinvex\Fort\Contracts;

use Rinvex\Repository\Contracts\CacheableContract;
use Rinvex\Repository\Contracts\RepositoryContract;

interface PersistenceRepositoryContract extends RepositoryContract, CacheableContract
{
    /**
     * Find an persistence by its token.
     *
     * @param string $token
     *
     * @return \Rinvex\Fort\Models\Persistence|null
     */
    public function findByToken($token);
}
