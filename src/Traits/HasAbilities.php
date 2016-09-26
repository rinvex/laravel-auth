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

namespace Rinvex\Fort\Traits;

use Rinvex\Fort\Models\Ability;
use Illuminate\Database\Eloquent\Model;

trait HasAbilities
{
    /**
     * Give the given ability to the given model.
     *
     * @param int|\Illuminate\Database\Eloquent\Model $id
     * @param string|array                            $action
     * @param string|array                            $resource
     *
     * @return $this
     */
    public function giveAbilityTo($id, $action, $resource)
    {
        // Find the given instance
        $instance = $id instanceof Model ?: $this->find($id);

        if ($instance) {
            // Fire the ability giving event
            event('rinvex.fort.ability.giving', [$instance, $action, $resource]);

            $repository = app('rinvex.fort.ability');

            if (is_string($action) && $action !== '*') {
                $repository = $repository->where('action', $action);
            }

            if (is_array($action)) {
                $repository = $repository->whereIn('action', $action);
            }

            if (is_string($resource) && $resource !== '*') {
                $repository = $repository->where('resource', $resource);
            }

            if (is_array($resource)) {
                $repository = $repository->whereIn('resource', $resource);
            }

            // Find the given abilities
            $abilities = $repository->findAll();

            // Sync abilities
            $instance->abilities()->syncWithoutDetaching($abilities);

            // Fire the ability given event
            event('rinvex.fort.ability.given', [$instance, $action, $resource]);
        }

        return $this;
    }

    /**
     * Revoke the given ability from the given model.
     *
     * @param int|\Illuminate\Database\Eloquent\Model $id
     * @param string|array                            $action
     * @param string|array                            $resource
     *
     * @return $this
     */
    public function revokeAbilityTo($id, $action, $resource)
    {
        // Find the given instance
        $instance = $id instanceof Model ?: $this->find($id);

        if ($instance) {
            // Fire the ability giving event
            event('rinvex.fort.ability.revoking', [$instance, $action, $resource]);

            $repository = app('rinvex.fort.ability');

            if (is_string($action) && $action !== '*') {
                $repository = $repository->where('action', $action);
            }

            if (is_array($action)) {
                $repository = $repository->whereIn('action', $action);
            }

            if (is_string($resource) && $resource !== '*') {
                $repository = $repository->where('resource', $resource);
            }

            if (is_array($resource)) {
                $repository = $repository->whereIn('resource', $resource);
            }

            // Find the given abilities
            $remove = $repository->findAll();

            // Sync abilities
            $current = $instance->abilities()->getRelatedIds()->toArray();
            $instance->abilities()->sync(array_diff($current, $remove->pluck('id')->toArray()));

            // Fire the ability given event
            event('rinvex.fort.ability.revoked', [$instance, $action, $resource]);
        }

        return $this;
    }
}
