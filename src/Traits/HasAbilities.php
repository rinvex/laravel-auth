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

trait HasAbilities
{
    /**
     * Give the given ability to a role/user.
     *
     * @param string|\Rinvex\Fort\Models\Ability $ability
     *
     * @return $this
     */
    public function giveAbilityTo($ability)
    {
        if ($this->hasAbilityTo($ability = $this->hydrateAbility($ability))) {
            return;
        }

        $this->abilities()->attach($ability);

        // Fire the ability given event
        event('rinvex.fort.ability.given', [$ability, $this]);

        return $this;
    }

    /**
     * Revoke the given ability from a role/user.
     *
     * @param string|\Rinvex\Fort\Models\Ability $ability
     *
     * @return $this
     */
    public function revokeAbilityTo($ability)
    {
        if (! $this->hasAbilityTo($ability = $this->hydrateAbility($ability))) {
            return;
        }

        $this->abilities()->detach($ability);

        // Fire the ability revoked event
        event('rinvex.fort.ability.revoked', [$ability, $this]);

        return $this;
    }

    /**
     * Hydrate ability.
     *
     * @param string|\Rinvex\Fort\Models\Ability $ability
     *
     * @return \Rinvex\Fort\Models\Ability
     */
    protected function hydrateAbility($ability)
    {
        return is_string($ability) ? $this->whereSlug($ability)->first() : $ability;
    }
}
