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
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

trait HasRoles
{
    use HasAbilities;

    /**
     * Assign the given role to the given entity.
     *
     * @param int|\Illuminate\Database\Eloquent\Model                              $id
     * @param string|array|\Rinvex\Fort\Models\Role|\Illuminate\Support\Collection $role
     *
     * @return $this
     */
    public function assignRole($id, $role)
    {
        // Find the given instance
        $instance = $id instanceof Model ?: $this->find($id);

        if ($instance) {
            $origRole = $role;

            // Fire the role given event
            event('rinvex.fort.role.assigning', [$instance, $origRole]);

            // Single role slug
            if (is_string($role)) {
                $role = app('rinvex.fort.role')->findBy('slug', $role);
            }

            // Single role model
            if ($role instanceof Role) {
                if ($this->hasRole($instance, $role)) {
                    return $this;
                }

                $instance->roles()->attach($role);
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

                $instance->roles()->syncWithoutDetaching($role);
            }

            // Fire the role given event
            event('rinvex.fort.role.assigned', [$instance, $origRole]);
        }

        return $this;
    }

    /**
     * Remove the given role from the given entity.
     *
     * @param int|\Illuminate\Database\Eloquent\Model                              $id
     * @param string|array|\Rinvex\Fort\Models\Role|\Illuminate\Support\Collection $role
     *
     * @return $this
     */
    public function removeRole($id, $role)
    {
        // Find the given instance
        $instance = $id instanceof Model ?: $this->find($id);

        if ($instance) {
            $origRole = $role;

            // Fire the role removed event
            event('rinvex.fort.role.removing', [$instance, $origRole]);

            // Single role slug
            if (is_string($role)) {
                $role = $instance->roles->contains('slug', $role);
            }

            // Single role model
            if ($role instanceof Role) {
                if (! $this->hasRole($instance, $role)) {
                    return $this;
                }

                $instance->roles()->detach($role);
            }

            // Array of role slugs
            if (is_array($role)) {
                $role = app('rinvex.fort.role')->findWhereIn(['slug', $role]);
            }

            // Collection of role models
            if ($role instanceof Collection) {
                $current = $instance->roles()->getRelatedIds()->toArray();
                $remove  = $role->map(function ($role) {
                    return $role instanceof Role ? $role->id : $role;
                })->toArray();

                $instance->roles()->sync(array_diff($current, $remove));
            }

            // Fire the role removed event
            event('rinvex.fort.role.removed', [$instance, $origRole]);
        }

        return $this;
    }

    /**
     * Determine if the given entity has (one of) the given roles.
     *
     * @param int|\Illuminate\Database\Eloquent\Model                              $id
     * @param string|array|\Rinvex\Fort\Models\Role|\Illuminate\Support\Collection $role
     *
     * @return bool
     */
    public function hasRole($id, $role)
    {
        // Find the given instance
        $instance = $id instanceof Model ?: $this->find($id);

        if ($instance) {
            // Single role slug
            if (is_string($role)) {
                return $instance->roles->contains('slug', $role);
            }

            // Single role model
            if ($role instanceof Role) {
                return $instance->roles->contains('slug', $role->slug);
            }

            // Array of role slugs
            if (is_array($role)) {
                $role = app('rinvex.fort.role')->findWhereIn(['slug', $role]);
            }

            // Collection of role models
            if ($role instanceof Collection) {
                return ! $role->intersect($instance->roles)->isEmpty();
            }
        }

        return false;
    }

    /**
     * Alias for `hasRole` method.
     *
     * @param int|\Illuminate\Database\Eloquent\Model                              $id
     * @param string|array|\Rinvex\Fort\Models\Role|\Illuminate\Support\Collection $role
     *
     * @return bool
     */
    public function hasAnyRole($id, $role)
    {
        return $this->hasRole($id, $role);
    }

    /**
     * Determine if the given entity has all of the given roles.
     *
     * @param int|\Illuminate\Database\Eloquent\Model                              $id
     * @param string|array|\Rinvex\Fort\Models\Role|\Illuminate\Support\Collection $role
     *
     * @return bool
     */
    public function hasAllRoles($id, $role)
    {
        // Find the given instance
        $instance = $id instanceof Model ?: $this->find($id);

        if ($instance) {
            // Single role slug
            if (is_string($role)) {
                return $instance->roles->contains('slug', $role);
            }

            // Single role model
            if ($role instanceof Role) {
                return $instance->roles->contains('slug', $role->slug);
            }

            // Array of role slugs
            if (is_array($role)) {
                $role = app('rinvex.fort.role')->findWhereIn(['slug', $role]);
            }

            // Collection of role models
            if ($role instanceof Collection) {
                return $instance->roles->toArray() == $role->toArray();
            }
        }

        return false;
    }
}
