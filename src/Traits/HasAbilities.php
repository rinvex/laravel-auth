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
        // Fire the ability giving event
        static::$dispatcher->fire('rinvex.fort.ability.giving', [$this, $action, $resource]);

        // Ability model
        $model = Ability::query();

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

        // Fire the ability given event
        static::$dispatcher->fire('rinvex.fort.ability.given', [$this, $action, $resource]);

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
        // Fire the ability giving event
        static::$dispatcher->fire('rinvex.fort.ability.revoking', [$this, $action, $resource]);

        // Ability model
        $model = Ability::query();

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

        // Fire the ability given event
        static::$dispatcher->fire('rinvex.fort.ability.revoked', [$this, $action, $resource]);

        return $this;
    }
}
