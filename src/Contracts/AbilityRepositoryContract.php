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

interface AbilityRepositoryContract extends RepositoryContract, CacheableContract
{
    /**
     * Define the abilities.
     *
     * @return $this
     */
    public function defineAbilities();

    /**
     * Find an ability by its slug.
     *
     * @param string $slug
     *
     * @return \Rinvex\Fort\Models\Ability|null
     */
    public function findBySlug($slug);
}
