<?php

declare(strict_types=1);

namespace Rinvex\Fort\Traits;

use Rinvex\Fort\Models\Role;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

trait HasRoles
{
    use HasAbilities;

    /**
     * Attach the given roles to the model.
     *
     * @param int|string|array|\ArrayAccess|\Rinvex\Fort\Models\Role $roles
     *
     * @return $this
     */
    public function assignRoles($roles)
    {
        $this->setRoles($roles, 'syncWithoutDetaching');

        return $this;
    }

    /**
     * Sync the given roles to the model.
     *
     * @param int|string|array|\ArrayAccess|\Rinvex\Fort\Models\Role $roles
     *
     * @return $this
     */
    public function syncRoles($roles)
    {
        $this->setRoles($roles, 'sync');

        return $this;
    }

    /**
     * Detach the given roles from the model.
     *
     * @param int|string|array|\ArrayAccess|\Rinvex\Fort\Models\Role $roles
     *
     * @return $this
     */
    public function removeRoles($roles)
    {
        $this->setRoles($roles, 'detach');

        return $this;
    }

    /**
     * Set the given role(s) to the model.
     *
     * @param int|string|array|\ArrayAccess|\Rinvex\Fort\Models\Role $roles
     * @param string                                                 $process
     *
     * @return bool
     */
    protected function setRoles($roles, string $process)
    {
        // Guess event name
        $event = $process === 'syncWithoutDetaching' ? 'attach' : $process;

        // If the "attaching/syncing/detaching" event returns false we'll cancel this operation and
        // return false, indicating that the attaching/syncing/detaching failed. This provides a
        // chance for any listeners to cancel save operations if validations fail or whatever.
        if ($this->fireModelEvent($event.'ing') === false) {
            return false;
        }

        // Hydrate Roles
        $roles = $this->hydrateRoles($roles)->pluck('id')->toArray();

        // Set roles
        $this->roles()->$process($roles);

        // Fire the roles attached/synced/detached event
        $this->fireModelEvent($event.'ed', false);

        return true;
    }

    /**
     * Determine if the entity has (one of) the given roles.
     *
     * @param string|int|array|\Rinvex\Fort\Models\Role|\Illuminate\Support\Collection $roles
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
        if (is_int($roles)) {
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
     * @param string|int|array|\Rinvex\Fort\Models\Role|\Illuminate\Support\Collection $roles
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
     * @param string|int|array|\Rinvex\Fort\Models\Role|\Illuminate\Support\Collection $roles
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
            return $this->roles->pluck('slug')->count() === count($roles)
                   && $this->roles->pluck('slug')->diff($roles)->isEmpty();
        }

        // Array of role ids
        if (is_array($roles) && isset($roles[0]) && is_int($roles[0])) {
            return $this->roles->pluck('id')->count() === count($roles)
                   && $this->roles->pluck('id')->diff($roles)->isEmpty();
        }

        // Collection of role models
        if ($roles instanceof Collection) {
            return $this->roles->count() === $roles->count() && $this->roles->diff($roles)->isEmpty();
        }

        return false;
    }

    /**
     * Scope the user query to certain roles only.
     *
     * @param \Illuminate\Database\Eloquent\Builder                                    $query
     * @param string|int|array|\Rinvex\Fort\Models\Role|\Illuminate\Support\Collection $roles
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRole($query, $roles)
    {
        if (is_string($roles) || is_int($roles) || $roles instanceof Role) {
            $roles = [$roles];
        }

        return $query->whereHas('roles', function (Builder $query) use ($roles) {
            $query->where(function (Builder $query) use ($roles) {
                foreach ($roles as $role) {
                    $column = is_string($role) ? 'slug' : 'id';
                    $value = $role instanceof Role ? $role->id : $role;

                    $query->orWhere(config('rinvex.fort.tables.roles').'.'.$column, $value);
                }
            });
        });
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
        $isRolesStringBased = $this->isRolesStringBased($roles);
        $isRolesIntBased = $this->isRolesIntBased($roles);
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
