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

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Rinvex\Fort\Traits\HasAbilities;
use Watson\Validating\ValidatingTrait;
use Rinvex\Cacheable\CacheableEloquent;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * Rinvex\Fort\Models\Role.
 *
 * @property int                                                                         $id
 * @property string                                                                      $slug
 * @property string                                                                      $name
 * @property string                                                                      $description
 * @property \Carbon\Carbon                                                              $created_at
 * @property \Carbon\Carbon                                                              $updated_at
 * @property \Carbon\Carbon                                                              $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Rinvex\Fort\Models\Ability[] $abilities
 * @property-read \Illuminate\Database\Eloquent\Collection|\Rinvex\Fort\Models\User[]    $users
 * @property-read array                                                                  $ability_list
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
    use HasSlug;
    use HasAbilities;
    use ValidatingTrait;
    use HasTranslations;
    use CacheableEloquent;

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
     * {@inheritdoc}
     */
    protected $observables = ['validating', 'validated'];

    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    public $translatable = [
        'name',
        'description',
    ];

    /**
     * The default rules that the model will validate against.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Whether the model should throw a ValidationException if it fails validation.
     *
     * @var bool
     */
    protected $throwValidationExceptions = true;

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
        $this->setRules([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'slug' => 'required|alpha_dash|unique:'.config('rinvex.fort.tables.roles').',slug',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function boot()
    {
        parent::boot();

        if (isset(static::$dispatcher)) {
            // Early auto generate slugs before validation
            static::$dispatcher->listen('eloquent.validating: '.static::class, function ($model, $event) {
                if (! $model->slug) {
                    if ($model->exists) {
                        $model->generateSlugOnCreate();
                    } else {
                        $model->generateSlugOnUpdate();
                    }
                }
            });
        }
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
     * Set the translatable name attribute.
     *
     * @param string $value
     *
     * @return void
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = json_encode(! is_array($value) ? [app()->getLocale() => $value] : $value);
    }

    /**
     * Set the translatable description attribute.
     *
     * @param string $value
     *
     * @return void
     */
    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = ! empty($value) ? json_encode(! is_array($value) ? [app()->getLocale() => $value] : $value) : null;
    }

    /**
     * Enforce clean slugs.
     *
     * @param string $value
     *
     * @return void
     */
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = str_slug($value);
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

    /**
     * Get the options for generating the slug.
     *
     * @return \Spatie\Sluggable\SlugOptions
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
                          ->generateSlugsFrom('name')
                          ->saveSlugsTo('slug');
    }
}
