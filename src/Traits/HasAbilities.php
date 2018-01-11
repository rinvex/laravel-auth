<?php

declare(strict_types=1);

namespace Rinvex\Fort\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;

trait HasAbilities
{
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
     * Boot the HasAbilities trait for the model.
     *
     * @return void
     */
    public static function bootHasAbilities()
    {
        static::deleted(function (self $model) {
            $model->abilities()->detach();
        });
    }

    /**
     * Attach the given abilities to the model.
     *
     * @param mixed $abilities
     *
     * @return void
     */
    public function setAbilitiesAttribute($abilities)
    {
        static::saved(function (self $model) use ($abilities) {
            $model->syncAbilities($abilities);
        });
    }

    /**
     * Attach the given abilities to the model.
     *
     * @param mixed $abilities
     *
     * @return $this
     */
    public function grantAbilities($abilities)
    {
        // Use 'sync' not 'attach' to avoid Integrity constraint violation
        $this->abilities()->sync($this->parseAbilities($abilities), false);

        return $this;
    }

    /**
     * Sync the given abilities to the model.
     *
     * @param mixed $abilities
     * @param bool  $detaching
     *
     * @return $this
     */
    public function syncAbilities($abilities, bool $detaching = true)
    {
        $this->abilities()->sync($this->parseAbilities($abilities), $detaching);

        return $this;
    }

    /**
     * Detach the given abilities from the model.
     *
     * @param mixed $abilities
     *
     * @return $this
     */
    public function revokeAbilities($abilities = null)
    {
        ! $abilities || $abilities = $this->parseAbilities($abilities);

        $this->abilities()->detach($abilities);

        return $this;
    }

    /**
     * Parse slugged abilities.
     *
     * @param mixed $abilities
     *
     * @return array
     */
    protected function parseSluggedAbilities($abilities)
    {
        $model = app('rinvex.fort.ability')->query();

        collect($abilities)->map(function ($item) use ($model) {
            return $model->when(mb_strpos($item, '-') !== false, function (Builder $builder) use ($item) {
                $builder->orWhere(function (Builder $builder) use ($item) {
                    $builder->where('action', explode('-', $item)[0])->where('resource', explode('-', $item)[1]);
                });
            });
        });

        return $model->get()->pluck('id')->toArray();
    }

    /**
     * Parse abilities.
     *
     * @param mixed $abilities
     *
     * @return array
     */
    protected function parseAbilities($abilities): array
    {
        ! $abilities instanceof Model || $abilities = [$abilities->getKey()];
        ! $abilities instanceof Collection || $abilities = $abilities->modelKeys();
        ! $abilities instanceof BaseCollection || $abilities = $abilities->toArray();

        if (is_string($abilities) || (is_array($abilities) && is_string(array_first($abilities)))) {
            $abilities = $this->parseSluggedAbilities($abilities);
        }

        return (array) $abilities;
    }
}
