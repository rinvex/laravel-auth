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

trait HasRoles
{
    use HasAbilities;

    /**
     * Assign the given role(s) to the entity.
     *
     * @param mixed $roles
     *
     * @return $this
     */
    public function assignRoles($roles)
    {
        // Role slug(s)
        if (is_string($roles) || (is_array($roles) && is_string($roles[0]))) {
            $roles = Role::whereIn('slug', (array) $roles)->get();
        }

        // Single role model
        if ($roles instanceof Role) {
            $roles = [$roles->id];
        }

        // Fire the role assigning event
        static::$dispatcher->fire('rinvex.fort.role.assigning', [$this, $roles]);

        // Assign roles
        $this->roles()->syncWithoutDetaching((array) $roles);

        // Fire the role assigned event
        static::$dispatcher->fire('rinvex.fort.role.assigned', [$this, $roles]);

        return $this;
    }

    /**
     * Sync the given role(s) to the entity.
     *
     * @param mixed $roles
     *
     * @return $this
     */
    public function syncRoles($roles)
    {
        // Role slug(s)
        if (is_string($roles) || (is_array($roles) && is_string($roles[0]))) {
            $roles = Role::whereIn('slug', (array) $roles)->get();
        }

        // Single role model
        if ($roles instanceof Role) {
            $roles = [$roles->id];
        }

        // Fire the role assigning event
        static::$dispatcher->fire('rinvex.fort.role.assigning', [$this, $roles]);

        // Assign roles
        $this->roles()->sync((array) $roles);

        // Fire the role assigned event
        static::$dispatcher->fire('rinvex.fort.role.assigned', [$this, $roles]);

        return $this;
    }

    /**
     * Remove the given role(s) from the entity.
     *
     * @param mixed $roles
     *
     * @return $this
     */
    public function removeRoles($roles)
    {
        // Array of role slugs
        if (is_string($roles) || (is_array($roles) && is_string($roles[0]))) {
            $roles = Role::whereIn('slug', (array) $roles)->get();
        }

        // Fire the role removed event
        static::$dispatcher->fire('rinvex.fort.role.removing', [$this, $roles]);

        // Detach roles
        $this->roles()->detach((array) $roles);

        // Fire the role removed event
        static::$dispatcher->fire('rinvex.fort.role.removed', [$this, $roles]);

        return $this;
    }

    /**
     * Determine if the entity has (one of) the given roles.
     *
     * @param mixed $roles
     *
     * @return bool
     */
    public function hasRole($roles)
    {
        // Single role slug
        if (is_string($roles)) {
            return $this->roles->contains('slug', $roles);
        }

        // Single role model
        if ($roles instanceof Role) {
            return $this->roles->contains('slug', $roles->slug);
        }

        // Array of role slugs
        if (is_array($roles) && is_string($roles[0])) {
            return $this->roles->pluck('slug')->intersect($roles)->isEmpty();
        }

        // Array of role Ids
        if (is_array($roles) && is_int($roles[0])) {
            return $this->roles->pluck('id')->intersect($roles)->isEmpty();
        }

        // Collection of role models
        if ($roles instanceof Collection) {
            return ! $roles->intersect($this->roles->pluck('slug'))->isEmpty();
        }

        return false;
    }

    /**
     * Alias for `hasRole` method.
     *
     * @param mixed $roles
     *
     * @return bool
     */
    public function hasAnyRole($roles)
    {
        return $this->hasRole($roles);
    }

    /**
     * Determine if the given entity has all of the given roles.
     *
     * @param mixed $roles
     *
     * @return bool
     */
    public function hasAllRoles($roles)
    {
        // Single role slug
        if (is_string($roles)) {
            return $this->roles->contains('slug', $roles);
        }

        // Single role model
        if ($roles instanceof Role) {
            return $this->roles->contains('slug', $roles->slug);
        }

        // Array of role slugs OR Collection of role models
        if ($roles instanceof Collection || (is_array($roles) && is_string($roles[0]))) {
            return $this->roles->pluck('slug')->count() == count($roles)
                   && $this->roles->pluck('slug')->diff($roles)->isEmpty();
        }

        return $this->roles->pluck('id')->count() == count($roles)
               && $this->roles->pluck('id')->diff($roles)->isEmpty();
    }
}
