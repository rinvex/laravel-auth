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

namespace Rinvex\Fort\Repositories;

use Illuminate\Contracts\Foundation\Application;
use Rinvex\Fort\Contracts\AbilityRepositoryContract;
use Rinvex\Repository\Repositories\EloquentRepository;

class AbilityRepository extends EloquentRepository implements AbilityRepositoryContract
{
    /**
     * create a new ability repository instance.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        $this->setContainer($app)
             ->setRepositoryId('rinvex.fort.ability')
             ->setModel($app['config']['rinvex.fort.models.ability']);
    }
}
