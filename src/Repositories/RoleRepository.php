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

use Rinvex\Fort\Traits\HasAbilities;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Foundation\Application;
use Rinvex\Fort\Contracts\RoleRepositoryContract;
use Rinvex\Repository\Repositories\EloquentRepository;

class RoleRepository extends EloquentRepository implements RoleRepositoryContract
{
    use HasAbilities;

    /**
     * create a new role repository instance.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->setContainer($app)
             ->setRepositoryId('rinvex.fort.role')
             ->setModel($app['config']['rinvex.fort.models.role']);
    }

    /**
     * {@inheritdoc}
     */
    public function findBySlug($slug)
    {
        return $this->findBy('slug', $slug);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        // Find the given instance
        $deleted  = false;
        $instance = $id instanceof Model ? $id : $this->find($id);

        if ($instance && $instance->users->count() === 0) {
            // Fire the deleted event
            $this->getContainer('events')->fire($this->getRepositoryId().'.entity.deleting', [$this, $instance]);

            // Delete the instance
            $deleted = $instance->delete();

            // Fire the deleted event
            $this->getContainer('events')->fire($this->getRepositoryId().'.entity.deleted', [$this, $instance]);
        }

        return [
            $deleted,
            $instance,
        ];
    }
}
