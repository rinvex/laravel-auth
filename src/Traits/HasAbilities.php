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
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

trait HasAbilities
{
    /**
     * Give the given ability to the given model.
     *
     * @param \Illuminate\Database\Eloquent\Model                                     $model
     * @param string|array|\Rinvex\Fort\Models\Ability|\Illuminate\Support\Collection $ability
     *
     * @return $this
     */
    public function giveAbilityTo(Model $model, $ability)
    {
        $origAbility = $ability;

        // Fire the ability giving event
        event('rinvex.fort.ability.giving', [$model, $origAbility]);

        // Single ability slug
        if (is_string($ability)) {
            $ability = app('rinvex.fort.ability')->findBy('slug', $ability);
        }

        // Single ability model
        if ($ability instanceof Ability) {
            if ($this->hasAbilityTo($model, $ability)) {
                return $this;
            }

            $model->abilities()->attach($ability);
        }

        // Array of ability slugs
        if (is_array($ability)) {
            $ability = app('rinvex.fort.ability')->findWhereIn(['slug', $ability]);
        }

        // Collection of ability models
        if ($ability instanceof Collection) {
            $ability = $ability->map(function ($ability) {
                return $ability instanceof Ability ? $ability->id : $ability;
            })->toArray();

            $model->abilities()->syncWithoutDetaching($ability);
        }

        // Fire the ability given event
        event('rinvex.fort.ability.given', [$model, $origAbility]);

        return $this;
    }

    /**
     * Revoke the given ability from the given model.
     *
     * @param \Illuminate\Database\Eloquent\Model                                     $model
     * @param string|array|\Rinvex\Fort\Models\Ability|\Illuminate\Support\Collection $ability
     *
     * @return $this
     */
    public function revokeAbilityTo(Model $model, $ability)
    {
        $origAbility = $ability;

        // Fire the ability revoking event
        event('rinvex.fort.ability.revoking', [$model, $origAbility]);

        // Single ability slug
        if (is_string($ability)) {
            $ability = $model->abilities->contains('slug', $ability);
        }

        // Single ability model
        if ($ability instanceof Ability) {
            if (! $this->hasAbilityTo($model, $ability)) {
                return $this;
            }

            $model->abilities()->detach($ability);
        }

        // Array of ability slugs
        if (is_array($ability)) {
            $ability = app('rinvex.fort.ability')->findWhereIn(['slug', $ability]);
        }

        // Collection of ability models
        if ($ability instanceof Collection) {
            $current = $model->abilities()->getRelatedIds()->toArray();
            $remove  = $ability->map(function ($ability) {
                return $ability instanceof Ability ? $ability->id : $ability;
            })->toArray();

            $model->abilities()->sync(array_diff($current, $remove));
        }

        // Fire the ability revoked event
        event('rinvex.fort.ability.revoked', [$model, $origAbility]);

        return $this;
    }

    /**
     * Determine if the given model may perform the given ability.
     *
     * @param \Illuminate\Database\Eloquent\Model                                     $model
     * @param string|array|\Rinvex\Fort\Models\Ability|\Illuminate\Support\Collection $role
     *
     * @return bool
     */
    public function hasAbilityTo(Model $model, $ability)
    {
        return $this->hasDirectAbility($model, $ability);
    }

    /**
     * Determine if the given model has the given ability.
     *
     * @param \Illuminate\Database\Eloquent\Model                                     $model
     * @param string|array|\Rinvex\Fort\Models\Ability|\Illuminate\Support\Collection $role
     *
     * @return bool
     */
    protected function hasDirectAbility(Model $model, $ability)
    {
        // Single ability slug
        if (is_string($ability)) {
            return $model->abilities->contains('slug', $ability) ? true : false;
        }

        // Single ability model
        if ($ability instanceof Ability) {
            return $model->abilities->contains('slug', $ability->slug);
        }

        // Array of ability slugs
        if (is_array($ability)) {
            return $model->abilities->pluck('slug')->intersect($ability)->isEmpty() ? false : true;
        }

        // Collection of ability models
        if ($ability instanceof Collection) {
            return $model->abilities->pluck('slug')->intersect($ability->pluck('slug')->toArray())->isEmpty() ? false : true;
        }

        return false;
    }
}
