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

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Foundation\Application;
use Rinvex\Repository\Repositories\EloquentRepository;
use Rinvex\Fort\Contracts\PersistenceRepositoryContract;

class PersistenceRepository extends EloquentRepository implements PersistenceRepositoryContract
{
    /**
     * The repository cache lifetime.
     *
     * @var float|int
     */
    protected $cacheLifetime = 0;

    /**
     * create a new persistence repository instance.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->setContainer($app)
             ->setRepositoryId('rinvex.fort.persistence')
             ->setModel($app['config']['rinvex.fort.models.persistence']);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        // Find the given instance
        $entity = $id instanceof Model ? $id : (is_int($id) ? $this->find($id) : $this->findBy('token', $id));

        return parent::delete($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByUser($id)
    {
        $deleted  = false;

        // Find the given instance
        $entity = $id instanceof Model ? $id : app('rinvex.fort.user')->find($id);

        if ($entity) {
            // Fire the deleted event
            $this->getContainer('events')->fire($this->getRepositoryId().'.entity.deleting', [$this, $entity]);

            // Delete the instance
            $deleted = app('rinvex.fort.persistence')->whereUserId($entity->id)->delete();

            // Fire the deleted event
            $this->getContainer('events')->fire($this->getRepositoryId().'.entity.deleted', [$this, $entity]);
        }

        return $deleted ? $entity : $deleted;
    }
}
