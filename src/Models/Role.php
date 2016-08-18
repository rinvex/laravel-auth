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

namespace Rinvex\Fort\Models;

use Rinvex\Fort\Traits\HasAbilities;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasAbilities, SoftDeletes;

    /**
     * {@inheritdoc}
     */
    protected $dates = ['deleted_at'];

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     *
     * @return       void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('rinvex.fort.tables.roles'));
    }

    /**
     * A role may be given various abilities.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function abilities()
    {
        return $this->belongsToMany(config('rinvex.fort.models.ability'), config('rinvex.fort.tables.ability_role'))
                    ->withTimestamps();
    }

    /**
     * A role may be assigned to various users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(config('rinvex.fort.models.user'), config('rinvex.fort.tables.role_user'))
                    ->withTimestamps();
    }

    /**
     * Determine if the role has the given ability.
     *
     * @param \Rinvex\Fort\Models\Ability|string $ability
     *
     * @return bool
     */
    public function hasAbilityTo($ability)
    {
        if (is_string($ability)) {
            $ability = $this->whereSlug($ability)->first();
        }

        return $this->abilities->contains('id', $ability->id);
    }
}
