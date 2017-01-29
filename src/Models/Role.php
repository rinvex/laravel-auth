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
use Rinvex\Cacheable\CacheableEloquent;
use Illuminate\Database\Eloquent\Model;

/**
 * Rinvex\Fort\Models\Role.
 *
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property string $description
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Rinvex\Fort\Models\Ability[] $abilities
 * @property-read \Illuminate\Database\Eloquent\Collection|\Rinvex\Fort\Models\User[] $users
 * @property-read array $ability_list
 *
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\Role whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\Role whereSlug($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\Role whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\Role whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\Role whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\Role whereDeletedAt($value)
 * @mixin \Illuminate\Database\Eloquent\Model
 */
class Role extends Model
{
    use HasAbilities;
    use CacheableEloquent;

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
        $this->addObservableEvents(['attaching', 'attached', 'syncing', 'synced', 'detaching', 'detached']);
    }

    /**
     * Register an attaching role event with the dispatcher.
     *
     * @param \Closure|string $callback
     *
     * @return void
     */
    public static function attaching($callback)
    {
        static::registerModelEvent('attaching', $callback);
    }

    /**
     * Register an attached role event with the dispatcher.
     *
     * @param \Closure|string $callback
     *
     * @return void
     */
    public static function attached($callback)
    {
        static::registerModelEvent('attached', $callback);
    }

    /**
     * Register a syncing role event with the dispatcher.
     *
     * @param \Closure|string $callback
     *
     * @return void
     */
    public static function syncing($callback)
    {
        static::registerModelEvent('syncing', $callback);
    }

    /**
     * Register a synced role event with the dispatcher.
     *
     * @param \Closure|string $callback
     *
     * @return void
     */
    public static function synced($callback)
    {
        static::registerModelEvent('synced', $callback);
    }

    /**
     * Register a detaching role event with the dispatcher.
     *
     * @param \Closure|string $callback
     *
     * @return void
     */
    public static function detaching($callback)
    {
        static::registerModelEvent('detaching', $callback);
    }

    /**
     * Register a detached role event with the dispatcher.
     *
     * @param \Closure|string $callback
     *
     * @return void
     */
    public static function detached($callback)
    {
        static::registerModelEvent('detached', $callback);
    }

    /**
     * A role may be given various abilities.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function abilities()
    {
        return $this->belongsToMany(config('rinvex.fort.models.ability'), config('rinvex.fort.tables.ability_role'), 'role_id', 'ability_id')
                    ->withTimestamps();
    }

    /**
     * A role may be assigned to various users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(config('rinvex.fort.models.user'), config('rinvex.fort.tables.role_user'), 'role_id', 'user_id')
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
