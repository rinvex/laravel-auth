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

trait HasRoles
{
    use HasAbilities;

    /**
     * Assign the given role to the user.
     *
     * @param string|\Rinvex\Fort\Models\Role $role
     *
     * @return $this
     */
    public function assignRole($role)
    {
        if ($this->hasRole($role = $this->hydrateRole($role))) {
            return;
        }

        $this->roles()->attach($role);

        // Fire the role assigned event
        event('rinvex.fort.role.assigned', [$role, $this]);

        return $this;
    }

    /**
     * Remove the given role from the user.
     *
     * @param string|\Rinvex\Fort\Models\Role $role
     *
     * @return $this
     */
    public function removeRole($role)
    {
        if (! $this->hasRole($role = $this->hydrateRole($role))) {
            return;
        }

        $this->roles()->detach($role);

        // Fire the role removed event
        event('rinvex.fort.role.removed', [$role, $this]);

        return $this;
    }

    /**
     * Determine if the user has (one of) the given role(s).
     *
     * @param string|array|\Rinvex\Fort\Models\Role|\Illuminate\Support\Collection $roles
     *
     * @return bool
     */
    public function hasRole($roles)
    {
        if (is_string($roles)) {
            return $this->roles->contains('slug', $roles);
        }

        if ($roles instanceof Role) {
            return $this->roles->contains('id', $roles->id);
        }

        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }

            return false;
        }

        return (bool) $roles->intersect($this->roles)->count();
    }

    /**
     * Determine if the user has any of the given role(s).
     *
     * @param string|array|\Rinvex\Fort\Models\Role|\Illuminate\Support\Collection $roles
     *
     * @return bool
     */
    public function hasAnyRole($roles)
    {
        return $this->hasRole($roles);
    }

    /**
     * Determine if the user has all of the given role(s).
     *
     * @param string|\Rinvex\Fort\Models\Role|\Illuminate\Support\Collection $roles
     *
     * @return bool
     */
    public function hasAllRoles($roles)
    {
        if (is_string($roles)) {
            return $this->roles->contains('slug', $roles);
        }

        if ($roles instanceof Role) {
            return $this->roles->contains('id', $roles->id);
        }

        $roles = collect()->make($roles)->map(function ($role) {
            return $role instanceof Role ? $role->slug : $role;
        });

        return $roles->intersect($this->roles->lists('slug')) == $roles;
    }

    /**
     * Determine if the user may perform the given ability.
     *
     * @param string|\Rinvex\Fort\Models\Ability $ability
     *
     * @return bool
     */
    public function hasAbilityTo($ability)
    {
        if (is_string($ability)) {
            $ability = $this->whereSlug($ability)->first();
        }

        return $this->hasDirectAbility($ability) || $this->hasAbilityViaRole($ability);
    }

    /**
     * Determine if the user has, via roles, the given ability.
     *
     * @param \Rinvex\Fort\Models\Ability $ability
     *
     * @return bool
     */
    protected function hasAbilityViaRole(Ability $ability)
    {
        return $this->hasRole($ability->roles);
    }

    /**
     * Determine if the user has the given ability.
     *
     * @param string|\Rinvex\Fort\Models\Ability $ability
     *
     * @return bool
     */
    protected function hasDirectAbility($ability)
    {
        if (is_string($ability)) {
            $ability = $this->whereSlug($ability)->first();

            if (! $ability) {
                return false;
            }
        }

        return $this->abilities->contains('id', $ability->id);
    }

    /**
     * Hydrate role.
     *
     * @param string|\Rinvex\Fort\Models\Role $role
     *
     * @return \Rinvex\Fort\Models\Role
     */
    protected function hydrateRole($role)
    {
        return is_string($role) ? $this->whereSlug($role)->first() : $role;
    }
}
