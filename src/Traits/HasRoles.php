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

use Rinvex\Fort\Models\Role;
use Rinvex\Fort\Models\Ability;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

trait HasRoles
{
    use HasAbilities;

    /**
     * Assign the given role to the given model.
     *
     * @param \Illuminate\Database\Eloquent\Model                                  $model
     * @param string|array|\Rinvex\Fort\Models\Role|\Illuminate\Support\Collection $role
     *
     * @return $this
     */
    public function assignRole(Model $model, $role)
    {
        $origRole = $role;

        // Fire the role given event
        event('rinvex.fort.role.assigning', [$model, $origRole]);

        // Single role slug
        if (is_string($role)) {
            $role = app('rinvex.fort.role')->findBy('slug', $role);
        }

        // Single role model
        if ($role instanceof Role) {
            if ($this->hasRole($model, $role)) {
                return $this;
            }

            $model->roles()->attach($role);
        }

        // Array of role slugs
        if (is_array($role)) {
            $role = app('rinvex.fort.role')->findWhereIn(['slug', $role]);
        }

        // Collection of role models
        if ($role instanceof Collection) {
            $role = $role->map(function ($role) {
                return $role instanceof Role ? $role->id : $role;
            })->toArray();

            $model->roles()->syncWithoutDetaching($role);
        }

        // Fire the role given event
        event('rinvex.fort.role.assigned', [$model, $origRole]);

        return $this;
    }

    /**
     * Remove the given role from the given model.
     *
     * @param \Illuminate\Database\Eloquent\Model                                  $model
     * @param string|array|\Rinvex\Fort\Models\Role|\Illuminate\Support\Collection $role
     *
     * @return $this
     */
    public function removeRole(Model $model, $role)
    {
        $origRole = $role;

        // Fire the role removed event
        event('rinvex.fort.role.removing', [$model, $origRole]);

        // Single role slug
        if (is_string($role)) {
            $role = $model->roles->contains('slug', $role);
        }

        // Single role model
        if ($role instanceof Role) {
            if (! $this->hasRole($model, $role)) {
                return $this;
            }

            $model->roles()->detach($role);
        }

        // Array of role slugs
        if (is_array($role)) {
            $role = app('rinvex.fort.role')->findWhereIn(['slug', $role]);
        }

        // Collection of role models
        if ($role instanceof Collection) {
            $current = $model->roles()->getRelatedIds()->toArray();
            $remove  = $role->map(function ($role) {
                return $role instanceof Role ? $role->id : $role;
            })->toArray();

            $model->roles()->sync(array_diff($current, $remove));
        }

        // Fire the role removed event
        event('rinvex.fort.role.removed', [$model, $origRole]);

        return $this;
    }

    /**
     * Determine if the given model has (one of) the given role.
     *
     * @param \Illuminate\Database\Eloquent\Model                                  $model
     * @param string|array|\Rinvex\Fort\Models\Role|\Illuminate\Support\Collection $role
     *
     * @return bool
     */
    public function hasRole(Model $model, $role)
    {
        // Single role slug
        if (is_string($role)) {
            return $model->roles->contains('slug', $role);
        }

        // Single role model
        if ($role instanceof Role) {
            return $model->roles->contains('slug', $role->slug);
        }

        // Array of role slugs
        if (is_array($role)) {
            $role = app('rinvex.fort.role')->findWhereIn(['slug', $role]);
        }

        // Collection of role models
        if ($role instanceof Collection) {
            return ! $role->intersect($model->roles)->isEmpty();
        }

        return false;
    }

    /**
     * Alias for `hasRole` method.
     *
     * @param \Illuminate\Database\Eloquent\Model                                  $model
     * @param string|array|\Rinvex\Fort\Models\Role|\Illuminate\Support\Collection $role
     *
     * @return bool
     */
    public function hasAnyRole(Model $model, $role)
    {
        return $this->hasRole($model, $role);
    }

    /**
     * Determine if the given model has all of the given role.
     *
     * @param \Illuminate\Database\Eloquent\Model                                  $model
     * @param string|array|\Rinvex\Fort\Models\Role|\Illuminate\Support\Collection $role
     *
     * @return bool
     */
    public function hasAllRoles(Model $model, $role)
    {
        // Single role slug
        if (is_string($role)) {
            return $model->roles->contains('slug', $role);
        }

        // Single role model
        if ($role instanceof Role) {
            return $model->roles->contains('slug', $role->slug);
        }

        // Array of role slugs
        if (is_array($role)) {
            $role = app('rinvex.fort.role')->findWhereIn(['slug', $role]);
        }

        // Collection of role models
        if ($role instanceof Collection) {
            return $model->roles->toArray() == $role->toArray();
        }

        return false;
    }

    /**
     * Determine if the given model has, via roles, the given ability.
     *
     * @param \Illuminate\Database\Eloquent\Model                                     $model
     * @param string|array|\Rinvex\Fort\Models\Ability|\Illuminate\Support\Collection $role
     *
     * @return bool
     */
    protected function hasAbilityViaRole(Model $model, $ability)
    {
        // Single ability slug
        if (is_string($ability)) {
            $ability = app('rinvex.fort.ability')->with(['roles'])->findBy('slug', $ability);
        }

        // Single ability model
        if ($ability instanceof Ability) {
            return $this->hasRole($model, $ability->roles);
        }

        // Array of ability slugs
        if (is_array($ability)) {
            $ability = app('rinvex.fort.ability')->with(['roles'])->findWhereIn(['slug', $ability]);
        }

        // Collection of ability models
        if ($ability instanceof Collection) {
            $roles = $ability->pluck('roles')->flatten()->pluck('slug')->unique()->toArray();

            return $this->hasRole($model, $roles);
        }

        return false;
    }
}
