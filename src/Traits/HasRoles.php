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
     * Attach the given role(s) to the model.
     *
     * @param int|string|array|\ArrayAccess|\Rinvex\Fort\Models\Role $roles
     *
     * @return $this
     */
    public function assignRoles($roles)
    {
        static::setRoles('syncWithoutDetaching', $roles);

        return $this;
    }

    /**
     * Sync the given role(s) to the model.
     *
     * @param int|string|array|\ArrayAccess|\Rinvex\Fort\Models\Role $roles
     *
     * @return $this
     */
    public function syncRoles($roles)
    {
        static::setRoles('sync', $roles);

        return $this;
    }

    /**
     * Detach the given role(s) from the model.
     *
     * @param int|string|array|\ArrayAccess|\Rinvex\Fort\Models\Role $roles
     *
     * @return $this
     */
    public function removeRoles($roles)
    {
        static::setRoles('detach', $roles);

        return $this;
    }

    /**
     * Set the given role(s) to the model.
     *
     * @param int|string|array|\ArrayAccess|\Rinvex\Fort\Models\Role $roles
     * @param string                                                 $action
     *
     * @return void
     */
    protected function setRoles($roles, string $action)
    {
        // Fix exceptional event name
        $event = $action == 'syncWithoutDetaching' ? 'attach' : $action;

        // Hydrate Roles
        $roles = static::hydrateRoles($roles)->pluck('id')->toArray();


        // Fire the role syncing event
        static::$dispatcher->fire("rinvex.fort.role.{$event}ing", [$this, $roles]);

        // Set roles
        $this->roles()->$action($roles);

        // Fire the role synced event
        static::$dispatcher->fire("rinvex.fort.role.{$event}ed", [$this, $roles]);
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

        // Single role id
        if (is_string($roles)) {
            return $this->roles->contains('id', $roles);
        }

        // Single role model
        if ($roles instanceof Role) {
            return $this->roles->contains('slug', $roles->slug);
        }

        // Array of role slugs
        if (is_array($roles) && isset($roles[0]) && is_string($roles[0])) {
            return ! $this->roles->pluck('slug')->intersect($roles)->isEmpty();
        }

        // Array of role Ids
        if (is_array($roles) && isset($roles[0]) && is_int($roles[0])) {
            return ! $this->roles->pluck('id')->intersect($roles)->isEmpty();
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

        // Single role id
        if (is_int($roles)) {
            return $this->roles->contains('id', $roles);
        }

        // Single role model
        if ($roles instanceof Role) {
            return $this->roles->contains('slug', $roles->slug);
        }

        // Array of role slugs
        if (is_array($roles) && isset($roles[0]) && is_string($roles[0])) {
            return $this->roles->pluck('slug')->count() == count($roles)
                   && $this->roles->pluck('slug')->diff($roles)->isEmpty();
        }

        // Array of role ids
        if (is_array($roles) && isset($roles[0]) && is_int($roles[0])) {
            return $this->roles->pluck('id')->count() == count($roles)
                   && $this->roles->pluck('id')->diff($roles)->isEmpty();
        }

        // Collection of role models
        if ($roles instanceof Collection) {
            return $this->roles->count() == $roles->count() && $this->roles->diff($roles)->isEmpty();
        }

        return false;
    }

    /**
     * Hydrate roles.
     *
     * @param int|string|array|\ArrayAccess|\Rinvex\Fort\Models\Role $roles
     *
     * @return \Illuminate\Support\Collection
     */
    protected function hydrateRoles($roles)
    {
        $isRolesStringBased = static::isRolesStringBased($roles);
        $isRolesIntBased = static::isRolesIntBased($roles);
        $field = $isRolesStringBased ? 'slug' : 'id';

        return $isRolesStringBased || $isRolesIntBased ? Role::whereIn($field, (array) $roles)->get() : collect($roles);
    }

    /**
     * Determine if the given role(ies) are string based.
     *
     * @param int|string|array|\ArrayAccess|\Rinvex\Fort\Models\Role $roles
     *
     * @return bool
     */
    protected function isRolesStringBased($roles)
    {
        return is_string($roles) || (is_array($roles) && isset($roles[0]) && is_string($roles[0]));
    }

    /**
     * Determine if the given role(ies) are integer based.
     *
     * @param int|string|array|\ArrayAccess|\Rinvex\Fort\Models\Role $roles
     *
     * @return bool
     */
    protected function isRolesIntBased($roles)
    {
        return is_int($roles) || (is_array($roles) && isset($roles[0]) && is_int($roles[0]));
    }
}
