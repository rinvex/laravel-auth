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

trait HasAbilities
{
    /**
     * Grant the given ability to the entity.
     *
     * @param string|array $action
     * @param string|array $resource
     *
     * @return $this
     */
    public function grantAbilityTo($action, $resource)
    {
        // Ability model
        $model = Ability::query();

        // Fire the ability granting event
        static::$dispatcher->fire('rinvex.fort.ability.granting', [$this, $action, $resource]);

        if (is_string($action) && $action !== '*') {
            $model->where('action', $action);
        }

        if (is_array($action)) {
            $model->whereIn('action', $action);
        }

        if (is_string($resource) && $resource !== '*') {
            $model->where('resource', $resource);
        }

        if (is_array($resource)) {
            $model->whereIn('resource', $resource);
        }

        // Find the given abilities
        $abilities = $model->get();

        // Sync abilities
        $this->abilities()->syncWithoutDetaching($abilities);

        // Fire the ability granted event
        static::$dispatcher->fire('rinvex.fort.ability.granted', [$this, $action, $resource]);

        return $this;
    }

    /**
     * Revoke the given ability from the entity.
     *
     * @param string|array $action
     * @param string|array $resource
     *
     * @return $this
     */
    public function revokeAbilityTo($action, $resource)
    {
        // Ability model
        $model = Ability::query();

        // Fire the ability revoking event
        static::$dispatcher->fire('rinvex.fort.ability.revoking', [$this, $action, $resource]);

        if (is_string($action) && $action !== '*') {
            $model->where('action', $action);
        }

        if (is_array($action)) {
            $model->whereIn('action', $action);
        }

        if (is_string($resource) && $resource !== '*') {
            $model->where('resource', $resource);
        }

        if (is_array($resource)) {
            $model->whereIn('resource', $resource);
        }

        // Find the given abilities
        $abilities = $model->get();

        // Sync abilities
        $this->abilities()->detach($abilities);

        // Fire the ability revoked event
        static::$dispatcher->fire('rinvex.fort.ability.revoked', [$this, $action, $resource]);

        return $this;
    }
}
