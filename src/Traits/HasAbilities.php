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

trait HasAbilities
{
    /**
     * Give the given ability to a role/user.
     *
     * @param string|array|\Rinvex\Fort\Models\Ability|\Illuminate\Support\Collection $ability
     *
     * @return $this
     */
    public function giveAbilityTo($ability)
    {
        $origAbility = $ability;

        // Fire the ability giving event
        event('rinvex.fort.ability.giving', [$origAbility, $this]);

        // Single ability slug
        if (is_string($ability)) {
            $ability = app('rinvex.fort.ability')->whereSlug($ability)->first();
        }

        // Single ability model
        if ($ability instanceof Ability) {
            if ($this->hasAbilityTo($ability)) {
                return $this;
            }

            $this->abilities()->attach($ability);
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

            $this->abilities()->syncWithoutDetaching($ability);
        }

        // Fire the ability given event
        event('rinvex.fort.ability.given', [$origAbility, $this]);

        return $this;
    }

    /**
     * Revoke the given ability from a role/user.
     *
     * @param string|array|\Rinvex\Fort\Models\Ability|\Illuminate\Support\Collection $ability
     *
     * @return $this
     */
    public function revokeAbilityTo($ability)
    {
        $origAbility = $ability;

        // Fire the ability revoking event
        event('rinvex.fort.ability.revoking', [$origAbility, $this]);

        // Single ability slug
        if (is_string($ability)) {
            $ability = $this->abilities()->whereSlug($ability)->first();
        }

        // Single ability model
        if ($ability instanceof Ability) {
            if (! $this->hasAbilityTo($ability)) {
                return $this;
            }

            $this->abilities()->detach($ability);
        }

        // Array of ability slugs
        if (is_array($ability)) {
            $ability = app('rinvex.fort.ability')->findWhereIn(['slug', $ability]);
        }

        // Collection of ability models
        if ($ability instanceof Collection) {
            $remove = $ability->map(function ($ability) {
                return $ability instanceof Ability ? $ability->id : $ability;
            })->toArray();

            $this->abilities()->sync(array_diff($this->abilities()->getRelatedIds()->toArray(), $remove));
        }

        // Fire the ability revoked event
        event('rinvex.fort.ability.revoked', [$origAbility, $this]);

        return $this;
    }

    /**
     * Determine if the user may perform the given ability.
     *
     * @param string|array|\Rinvex\Fort\Models\Ability|\Illuminate\Support\Collection $role
     *
     * @return bool
     */
    public function hasAbilityTo($ability)
    {
        return $this->hasDirectAbility($ability);
    }

    /**
     * Determine if the user has the given ability.
     *
     * @param string|array|\Rinvex\Fort\Models\Ability|\Illuminate\Support\Collection $role
     *
     * @return bool
     */
    protected function hasDirectAbility($ability)
    {
        // Single ability slug
        if (is_string($ability)) {
            return $this->abilities()->whereSlug($ability)->first() ? true : false;
        }

        // Single ability model
        if ($ability instanceof Ability) {
            return $this->abilities->contains('slug', $ability->slug);
        }

        // Array of ability slugs
        if (is_array($ability)) {
            return $this->abilities->pluck('slug')->intersect($ability)->isEmpty() ? false : true;
        }

        // Collection of role models
        if ($ability instanceof Collection) {
            return $this->abilities->pluck('slug')->intersect($ability->pluck('slug')->toArray())->isEmpty() ? false : true;
        }

        return false;
    }
}
