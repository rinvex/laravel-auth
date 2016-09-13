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
    public function findByToken($token)
    {
        return $this->findBy('token', $token);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        // Find the given instance
        $deleted  = false;
        $instance = $id instanceof Model ? $id : (is_int($id) ? $this->find($id) : $this->findByToken($id));

        if ($instance) {
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

    /**
     * {@inheritdoc}
     */
    public function deleteByUser($id)
    {
        // Find the given instance
        $deleted  = false;
        $instance = $id instanceof Model ? $id : app('rinvex.fort.user')->find($id);

        if ($instance) {
            // Fire the deleted event
            $this->getContainer('events')->fire($this->getRepositoryId().'.entity.deleting', [$this, $instance]);

            // Delete by instance
            $deleted = app('rinvex.fort.persistence')->whereUserId($instance->id)->delete();

            // Fire the deleted event
            $this->getContainer('events')->fire($this->getRepositoryId().'.entity.deleted', [$this, $instance]);
        }

        return [
            $deleted,
            $instance,
        ];
    }
}
