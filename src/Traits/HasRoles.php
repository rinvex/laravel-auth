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

trait HasRoles
{
    use HasAbilities;

    /**
     * Assign the given role to the user.
     *
     * @param string|array|\Rinvex\Fort\Models\Role|\Illuminate\Support\Collection $role
     *
     * @return $this
     */
    public function assignRole($role)
    {
        $origRole = $role;

        // Fire the role given event
        event('rinvex.fort.role.assigning', [$origRole, $this]);

        // Single role slug
        if (is_string($role)) {
            $role = app('rinvex.fort.role')->whereSlug($role)->first();
        }

        // Single role model
        if ($role instanceof Role) {
            if ($this->hasRole($role)) {
                return $this;
            }

            $this->roles()->attach($role);
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

            $this->roles()->syncWithoutDetaching($role);
        }

        // Fire the role given event
        event('rinvex.fort.role.assigned', [$origRole, $this]);

        return $this;
    }

    /**
     * Remove the given role from the user.
     *
     * @param string|array|\Rinvex\Fort\Models\Role|\Illuminate\Support\Collection $role
     *
     * @return $this
     */
    public function removeRole($role)
    {
        $origRole = $role;

        // Fire the role removed event
        event('rinvex.fort.role.removing', [$origRole, $this]);

        // Single role slug
        if (is_string($role)) {
            $role = $this->roles()->whereSlug($role)->first();
        }

        // Single role model
        if ($role instanceof Role) {
            if (! $this->hasRole($role)) {
                return $this;
            }

            $this->roles()->detach($role);
        }

        // Array of role slugs
        if (is_array($role)) {
            $role = app('rinvex.fort.role')->findWhereIn(['slug', $role]);
        }

        // Collection of role models
        if ($role instanceof Collection) {
            $remove = $role->map(function ($role) {
                return $role instanceof Role ? $role->id : $role;
            })->toArray();

            $this->roles()->sync(array_diff($this->roles()->getRelatedIds()->toArray(), $remove));
        }

        // Fire the role removed event
        event('rinvex.fort.role.removed', [$origRole, $this]);

        return $this;
    }

    /**
     * Determine if the user has (one of) the given role(s).
     *
     * @param string|array|\Rinvex\Fort\Models\Role|\Illuminate\Support\Collection $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        // Single role slug
        if (is_string($role)) {
            return $this->roles->contains('slug', $role);
        }

        // Single role model
        if ($role instanceof Role) {
            return $this->roles->contains('slug', $role->slug);
        }

        // Array of role slugs
        if (is_array($role)) {
            $role = app('rinvex.fort.role')->findWhereIn(['slug', $role]);
        }

        // Collection of role models
        if ($role instanceof Collection) {
            return ! $role->intersect($this->roles)->isEmpty();
        }

        return false;
    }

    /**
     * Alias for `hasRole` method.
     * Determine if the user has any of the given role(s).
     *
     * @param string|array|\Rinvex\Fort\Models\Role|\Illuminate\Support\Collection $role
     *
     * @return bool
     */
    public function hasAnyRole($role)
    {
        return $this->hasRole($role);
    }

    /**
     * Determine if the user has all of the given role(s).
     *
     * @param string|array|\Rinvex\Fort\Models\Role|\Illuminate\Support\Collection $role
     *
     * @return bool
     */
    public function hasAllRoles($role)
    {
        // Single role slug
        if (is_string($role)) {
            return $this->roles->contains('slug', $role);
        }

        // Single role model
        if ($role instanceof Role) {
            return $this->roles->contains('slug', $role->slug);
        }

        // Array of role slugs
        if (is_array($role)) {
            $role = app('rinvex.fort.role')->findWhereIn(['slug', $role]);
        }

        // Collection of role models
        if ($role instanceof Collection) {
            return $role->diff($this->roles)->isEmpty();
        }

        return false;
    }

    /**
     * Determine if the user has, via roles, the given ability.
     *
     * @param string|array|\Rinvex\Fort\Models\Ability|\Illuminate\Support\Collection $role
     *
     * @return bool
     */
    public function hasAbilityViaRole($ability)
    {
        // Single ability slug
        if (is_string($ability)) {
            $ability = app('rinvex.fort.ability')->whereSlug($ability)->first();
        }

        // Single ability model
        if ($ability instanceof Ability) {
            return $this->hasRole($ability->roles);
        }

        // Array of ability slugs
        if (is_array($ability)) {
            $ability = app('rinvex.fort.ability')->findWhereIn(['slug', $ability]);
        }

        // Collection of ability models
        if ($ability instanceof Collection) {
            $roles = $ability->pluck('roles')->flatten()->pluck('slug');

            return $this->hasRole($roles->toArray());
        }

        return false;
    }
}
