<?php

declare(strict_types=1);

namespace Rinvex\Fort\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;

trait HasRoles
{
    use HasAbilities;

    /**
     * Register a saved model event with the dispatcher.
     *
     * @param \Closure|string $callback
     *
     * @return void
     */
    abstract public static function saved($callback);

    /**
     * Register a deleted model event with the dispatcher.
     *
     * @param \Closure|string $callback
     *
     * @return void
     */
    abstract public static function deleted($callback);

    /**
     * Boot the HasRoles trait for the model.
     *
     * @return void
     */
    public static function bootHasRoles()
    {
        static::deleted(function (self $model) {
            $model->roles()->detach();
        });
    }

    /**
     * Attach the given roles to the model.
     *
     * @param mixed $roles
     *
     * @return void
     */
    public function setRolesAttribute($roles): void
    {
        static::saved(function (self $model) use ($roles) {
            $model->syncRoles($roles);
        });
    }

    /**
     * Attach model roles.
     *
     * @param mixed $roles
     *
     * @return $this
     */
    public function assignRoles($roles)
    {
        // Use 'sync' not 'attach' to avoid Integrity constraint violation
        $this->roles()->sync($this->parseRoles($roles), false);

        return $this;
    }

    /**
     * Sync model roles.
     *
     * @param mixed $roles
     * @param bool  $detaching
     *
     * @return $this
     */
    public function syncRoles($roles, bool $detaching = true)
    {
        $this->roles()->sync($this->parseRoles($roles), $detaching);

        return $this;
    }

    /**
     * Detach model roles.
     *
     * @param mixed $roles
     *
     * @return $this
     */
    public function retractRoles($roles = null)
    {
        ! $roles || $roles = $this->parseRoles($roles);

        $this->roles()->detach($roles);

        return $this;
    }

    /**
     * Determine if the model has any of the given roles.
     *
     * @param mixed $roles
     *
     * @return bool
     */
    public function hasAnyRoles($roles): bool
    {
        $roles = $this->parseRoles($roles);

        return ! $this->roles->pluck('id')->intersect($roles)->isEmpty();
    }

    /**
     * Determine if the given entity has all of the given roles.
     *
     * @param mixed $roles
     *
     * @return bool
     */
    public function hasAllRoles($roles): bool
    {
        $roles = $this->parseRoles($roles);

        return collect($roles)->diff($this->roles->pluck('id'))->isEmpty();
    }

    /**
     * Scope query with all the given roles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param mixed                                 $roles
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAllRoles(Builder $builder, $roles): Builder
    {
        $roles = $this->parseRoles($roles);

        collect($roles)->each(function ($role) use ($builder) {
            $builder->whereHas('roles', function (Builder $builder) use ($role) {
                return $builder->where('id', $role);
            });
        });

        return $builder;
    }

    /**
     * Scope query with any of the given roles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param mixed                                 $roles
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAnyRoles(Builder $builder, $roles): Builder
    {
        $roles = $this->parseRoles($roles);

        return $builder->whereHas('roles', function (Builder $builder) use ($roles) {
            $builder->whereIn('id', $roles);
        });
    }

    /**
     * Scope query without any of the given roles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param mixed                                 $roles
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutRoles(Builder $builder, $roles): Builder
    {
        $roles = $this->parseRoles($roles);

        return $builder->whereDoesntHave('roles', function (Builder $builder) use ($roles) {
            $builder->whereIn('id', $roles);
        });
    }

    /**
     * Scope query without any roles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutAnyRoles(Builder $builder): Builder
    {
        return $builder->doesntHave('roles');
    }

    /**
     * Parse role IDs.
     *
     * @param mixed $roles
     *
     * @return array
     */
    protected function parseRoles($roles): array
    {
        (is_iterable($rawRoles) || is_null($rawRoles)) || $rawRoles = [$rawRoles];

        list($strings, $roles) = collect($rawRoles)->map(function ($role) {
            ! is_numeric($role) || $role = (int) $role;

            ! $role instanceof Model || $role = [$role->getKey()];
            ! $role instanceof Collection || $role = $role->modelKeys();
            ! $role instanceof BaseCollection || $role = $role->toArray();

            return $role;
        })->partition(function ($item) {
            return is_string($item);
        });

        return $roles->merge(app('rinvex.fort.role')->whereIn('slug', $roles)->get()->pluck('id'))->toArray();
    }
}
