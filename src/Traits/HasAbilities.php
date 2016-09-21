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
     * @param int|\Illuminate\Database\Eloquent\Model                                     $id
     * @param string|array|\Rinvex\Fort\Models\Ability|\Illuminate\Support\Collection $ability
     *
     * @return $this
     */
    public function giveAbilityTo($id, $ability)
    {
        // Find the given instance
        $instance = $id instanceof Model ?: $this->find($id);

        if ($instance) {
            $origAbility = $ability;

            // Fire the ability giving event
            event('rinvex.fort.ability.giving', [$instance, $origAbility]);

            // Single ability slug
            if (is_string($ability)) {
                $ability = app('rinvex.fort.ability')->findBy('action', $ability);
            }

            // Single ability model
            if ($ability instanceof Ability) {
                if ($instance->all_abilities->contains('action', $ability->action)) {
                    return $this;
                }

                $instance->abilities()->attach($ability);
            }

            // Array of ability slugs
            if (is_array($ability)) {
                $ability = app('rinvex.fort.ability')->findWhereIn(['action', $ability]);
            }

            // Collection of ability models
            if ($ability instanceof Collection) {
                $ability = $ability->map(function ($ability) {
                    return $ability instanceof Ability ? $ability->id : $ability;
                })->toArray();

                $instance->abilities()->syncWithoutDetaching($ability);
            }

            // Fire the ability given event
            event('rinvex.fort.ability.given', [$instance, $origAbility]);
        }

        return $this;
    }

    /**
     * Revoke the given ability from the given model.
     *
     * @param int|\Illuminate\Database\Eloquent\Model                                     $id
     * @param string|array|\Rinvex\Fort\Models\Ability|\Illuminate\Support\Collection $ability
     *
     * @return $this
     */
    public function revokeAbilityTo($id, $ability)
    {
        // Find the given instance
        $instance = $id instanceof Model ?: $this->find($id);

        if ($instance) {
            $origAbility = $ability;

            // Fire the ability revoking event
            event('rinvex.fort.ability.revoking', [$instance, $origAbility]);

            // Single ability slug
            if (is_string($ability)) {
                $ability = $instance->abilities->contains('action', $ability);
            }

            // Single ability model
            if ($ability instanceof Ability) {
                if (! $instance->all_abilities->contains('action', $ability->action)) {
                    return $this;
                }

                $instance->abilities()->detach($ability);
            }

            // Array of ability slugs
            if (is_array($ability)) {
                $ability = app('rinvex.fort.ability')->findWhereIn(['action', $ability]);
            }

            // Collection of ability models
            if ($ability instanceof Collection) {
                $current = $instance->abilities()->getRelatedIds()->toArray();
                $remove  = $ability->map(function ($ability) {
                    return $ability instanceof Ability ? $ability->id : $ability;
                })->toArray();

                $instance->abilities()->sync(array_diff($current, $remove));
            }

            // Fire the ability revoked event
            event('rinvex.fort.ability.revoked', [$instance, $origAbility]);
        }

        return $this;
    }
}
