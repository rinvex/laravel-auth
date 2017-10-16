<?php

declare(strict_types=1);

namespace Rinvex\Fort\Models;

use Illuminate\Database\Eloquent\Model;
use Rinvex\Cacheable\CacheableEloquent;
use Rinvex\Fort\Contracts\AbilityContract;
use Rinvex\Support\Traits\HasTranslations;
use Rinvex\Support\Traits\ValidatingTrait;

/**
 * Rinvex\Fort\Models\Ability.
 *
 * @property int                                                                      $id
 * @property string                                                                   $action
 * @property string                                                                   $resource
 * @property string                                                                   $policy
 * @property array                                                                    $name
 * @property array                                                                    $description
 * @property \Carbon\Carbon|null                                                      $created_at
 * @property \Carbon\Carbon|null                                                      $updated_at
 * @property \Carbon\Carbon|null                                                      $deleted_at
 * @property-read string                                                              $slug
 * @property \Illuminate\Database\Eloquent\Collection|\Rinvex\Fort\Models\Role[]      $roles
 * @property-read \Illuminate\Database\Eloquent\Collection|\Rinvex\Fort\Models\User[] $users
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Ability whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Ability whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Ability whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Ability whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Ability whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Ability whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Ability wherePolicy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Ability whereResource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Ability whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Ability extends Model implements AbilityContract
{
    use HasTranslations;
    use ValidatingTrait;
    use CacheableEloquent;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'name',
        'action',
        'resource',
        'policy',
        'description',
        'roles',
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'action' => 'string',
        'resource' => 'string',
        'policy' => 'string',
        'deleted_at' => 'datetime',
    ];

    /**
     * {@inheritdoc}
     */
    protected $observables = [
        'attaching',
        'attached',
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
     * {@inheritdoc}
     */
    protected $validationMessages = [
        'action.unique' => 'The combination of (action & resource) fields has already been taken.',
        'resource.unique' => 'The combination of (action & resource) fields has already been taken.',
    ];

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

        $this->setTable(config('rinvex.fort.tables.abilities'));
        $this->setRules([
            'name' => 'required|string|max:150',
            'action' => 'required|string|unique:'.config('rinvex.fort.tables.abilities').',action,NULL,id,resource,'.($this->resource ?? 'null'),
            'resource' => 'required|string|unique:'.config('rinvex.fort.tables.abilities').',resource,NULL,id,action,'.($this->action ?? 'null'),
            'policy' => 'nullable|string',
            'description' => 'nullable|string|max:10000',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected static function boot()
    {
        parent::boot();

        static::updated(function (self $ability) {
            app('rinvex.fort.role')->forgetCache();
            app('rinvex.fort.user')->forgetCache();
        });

        static::deleted(function (self $ability) {
            app('rinvex.fort.role')->forgetCache();
            app('rinvex.fort.user')->forgetCache();
        });

        static::attached(function (self $ability) {
            app('rinvex.fort.role')->forgetCache();
            app('rinvex.fort.user')->forgetCache();
        });

        static::detached(function (self $ability) {
            app('rinvex.fort.role')->forgetCache();
            app('rinvex.fort.user')->forgetCache();
        });
    }

    /**
     * Register an attaching ability event with the dispatcher.
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
     * Register an attached ability event with the dispatcher.
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
     * Register a detaching ability event with the dispatcher.
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
     * Register a detached ability event with the dispatcher.
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
     * An ability can be applied to roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(config('rinvex.fort.models.role'), config('rinvex.fort.tables.ability_role'), 'ability_id', 'role_id')
                    ->withTimestamps();
    }

    /**
     * An ability can be applied to users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        $userModel = config('auth.providers.'.config('auth.guards.'.config('auth.defaults.guard').'.provider').'.model');

        return $this->belongsToMany($userModel, config('rinvex.fort.tables.ability_user'), 'ability_id', 'user_id')
                    ->withTimestamps();
    }

    /**
     * Determine if the ability is super admin.
     *
     * @return bool
     */
    public function isSuperadmin()
    {
        return ! $this->policy && $this->resource === 'global' && $this->action === 'superadmin';
    }

    /**
     * Determine if the ability is protected.
     *
     * @return bool
     */
    public function isProtected()
    {
        return in_array($this->id, config('rinvex.fort.protected.abilities'));
    }

    /**
     * Get slug attribute out of ability's action & resource.
     *
     * @return string
     */
    public function getSlugAttribute()
    {
        return $this->action.'-'.$this->resource;
    }

    /**
     * Attach the ability roles.
     *
     * @param mixed $roles
     *
     * @return void
     */
    public function setRolesAttribute($roles)
    {
        static::saved(function (self $model) use ($roles) {
            $model->roles()->sync($roles);
        });
    }
}
