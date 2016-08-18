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

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Foundation\Application;
use Rinvex\Fort\Contracts\AbilityRepositoryContract;
use Rinvex\Repository\Repositories\EloquentRepository;

class AbilityRepository extends EloquentRepository implements AbilityRepositoryContract
{
    /**
     * create a new ability repository instance.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->setContainer($app)
             ->setRepositoryId('rinvex.fort.ability')
             ->setModel($app['config']['rinvex.fort.models.ability']);
    }

    /**
     * {@inheritdoc}
     */
    public function defineAbilities()
    {
        try {
            $this->findAll()->map(function ($ability) {
                $this->getContainer(Gate::class)->define($ability->slug, function ($user) use ($ability) {
                    return $user->hasAbilityTo($ability);
                });
            });

            // Fire the ability defined event
            $this->getContainer('events')->fire('rinvex.fort.ability.defined');
        } catch (Exception $e) {
            // Fail silently! Probably we had issues connecting to the database!!
            Log::alert($e->getMessage());
        }

        return $this;
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

        if ($instance && $instance->roles->count() === 0 && $instance->users->count() === 0) {
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
