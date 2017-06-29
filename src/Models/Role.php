<?php

declare(strict_types=1);

namespace Rinvex\Fort\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Rinvex\Fort\Traits\HasAbilities;
use Watson\Validating\ValidatingTrait;
use Illuminate\Database\Eloquent\Model;
use Rinvex\Cacheable\CacheableEloquent;
use Spatie\Translatable\HasTranslations;

/**
 * Rinvex\Fort\Models\Role.
 *
 * @property int                                                                      $id
 * @property string                                                                   $slug
 * @property array                                                                    $name
 * @property array                                                                    $description
 * @property \Carbon\Carbon|null                                                      $created_at
 * @property \Carbon\Carbon|null                                                      $updated_at
 * @property \Carbon\Carbon|null                                                      $deleted_at
 * @property \Illuminate\Database\Eloquent\Collection|\Rinvex\Fort\Models\Ability[]   $abilities
 * @property-read \Illuminate\Database\Eloquent\Collection|\Rinvex\Fort\Models\User[] $users
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Role whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Role whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Role whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Role whereUpdatedAt($value)
 * @mixin \Eloquent
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
    protected $dates = [
        'deleted_at',
    ];

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'slug',
        'name',
        'description',
        'abilities',
    ];

    /**
     * {@inheritdoc}
     */
    protected $with = ['abilities'];

    /**
     * {@inheritdoc}
     */
    protected $observables = [
        'attaching',
        'attached',
        'syncing',
        'synced',
        'detaching',
        'detached',
        'validating',
        'validated',
    ];

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
     * Whether the model should throw a
     * ValidationException if it fails validation.
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
        $this->setRules([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'slug' => 'required|alpha_dash|max:150|unique:'.config('rinvex.fort.tables.roles').',slug',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected static function boot()
    {
        parent::boot();

        static::updated(function (self $role) {
            Ability::forgetCache();
            User::forgetCache();
        });

        static::deleted(function (self $role) {
            Ability::forgetCache();
            User::forgetCache();
        });

        static::attached(function (self $role) {
            Ability::forgetCache();
            User::forgetCache();
        });

        static::synced(function (self $role) {
            Ability::forgetCache();
            User::forgetCache();
        });

        static::detached(function (self $role) {
            Ability::forgetCache();
            User::forgetCache();
        });

        // Auto generate slugs early before validation
        static::registerModelEvent('validating', function (self $role) {
            if (! $role->slug) {
                if ($role->exists && $role->getSlugOptions()->generateSlugsOnUpdate) {
                    $role->generateSlugOnUpdate();
                } elseif (! $role->exists && $role->getSlugOptions()->generateSlugsOnCreate) {
                    $role->generateSlugOnCreate();
                }
            }
        });
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
     * Register a validating role event with the dispatcher.
     *
     * @param \Closure|string $callback
     *
     * @return void
     */
    public static function validating($callback)
    {
        static::registerModelEvent('validating', $callback);
    }

    /**
     * Register a validated role event with the dispatcher.
     *
     * @param \Closure|string $callback
     *
     * @return void
     */
    public static function validated($callback)
    {
        static::registerModelEvent('validated', $callback);
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
        $userModel = config('auth.providers.'.config('auth.guards.'.config('auth.defaults.guard').'.provider').'.model');

        return $this->belongsToMany($userModel, config('rinvex.fort.tables.role_user'), 'role_id', 'user_id')
                    ->withTimestamps();
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
                          ->doNotGenerateSlugsOnUpdate()
                          ->generateSlugsFrom('name')
                          ->saveSlugsTo('slug');
    }

    /**
     * Attach the role abilities.
     *
     * @param mixed $abilities
     *
     * @return void
     */
    public function setAbilitiesAttribute($abilities)
    {
        static::saved(function (self $model) use ($abilities) {
            $model->abilities()->sync($abilities);
        });
    }
}
