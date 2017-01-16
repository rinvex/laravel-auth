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
        'slug',
        'name',
        'description',
    ];

    /**
     * {@inheritdoc}
     */
    protected $with = ['abilities'];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
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
     * Get the list of ability Ids.
     *
     * @return array
     */
    public function getAbilityListAttribute()
    {
        return $this->abilities->pluck('id')->toArray();
    }

    /**
     * Determine if the role is super admin.
     *
     * @return bool
     */
    public function isSuperadmin()
    {
        return $this->abilities->where('resource', 'global')->where('policy', null)->contains('action', 'superadmin');
    }

    /**
     * Determine if the role is protected.
     *
     * @return bool
     */
    public function isProtected()
    {
        return in_array($this->id, config('rinvex.fort.protected.roles'));
    }
}
