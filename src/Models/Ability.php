<?php

declare(strict_types=1);

namespace Rinvex\Fort\Models;

use Watson\Validating\ValidatingTrait;
use Illuminate\Database\Eloquent\Model;
use Rinvex\Cacheable\CacheableEloquent;
use Spatie\Translatable\HasTranslations;

/**
 * Rinvex\Fort\Models\Ability.
 *
 * @property int                                                                      $id
 * @property string                                                                   $action
 * @property string                                                                   $resource
 * @property string|null                                                              $policy
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
class Ability extends Model
{
    use HasTranslations;
    use ValidatingTrait;
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
        'action',
        'resource',
        'policy',
        'name',
        'description',
        'roles',
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
            'action' => 'required|unique:'.config('rinvex.fort.tables.abilities').',action,NULL,id,resource,'.($this->resource ?? 'null'),
            'resource' => 'required|unique:'.config('rinvex.fort.tables.abilities').',resource,NULL,id,action,'.($this->action ?? 'null'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected static function boot()
    {
        parent::boot();

        static::updated(function (self $ability) {
            Role::forgetCache();
            User::forgetCache();
        });

        static::deleted(function (self $ability) {
            Role::forgetCache();
            User::forgetCache();
        });

        static::attached(function (self $ability) {
            Role::forgetCache();
            User::forgetCache();
        });

        static::detached(function (self $ability) {
            Role::forgetCache();
            User::forgetCache();
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
     * Register a validating ability event with the dispatcher.
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
     * Register a validated ability event with the dispatcher.
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
     * Prepare a unique rule, adding the table name, column and model indetifier
     * if required.
     *
     * @param array  $parameters
     * @param string $field
     *
     * @return string
     */
    protected function prepareUniqueRule($parameters, $field)
    {
        // If the table name isn't set, infer it.
        if (empty($parameters[0])) {
            $parameters[0] = $this->getModel()->getTable();
        }

        // If the connection name isn't set but exists, infer it.
        if ((mb_strpos($parameters[0], '.') === false) && (($connectionName = $this->getModel()->getConnectionName()) !== null)) {
            $parameters[0] = $connectionName.'.'.$parameters[0];
        }

        // If the field name isn't get, infer it.
        if (! isset($parameters[1])) {
            $parameters[1] = $field;
        }

        if ($this->exists) {
            // If the identifier isn't set, infer it.
            if (! isset($parameters[2]) || mb_strtolower($parameters[2]) === 'null') {
                $parameters[2] = $this->getModel()->getKey();
            }

            // If the primary key isn't set, infer it.
            if (! isset($parameters[3])) {
                $parameters[3] = $this->getModel()->getKeyName();
            }

            // If the additional where clause isn't set, infer it.
            // Example: unique:abilities,resource,123,id,action,NULL
            foreach ($parameters as $key => $parameter) {
                if (mb_strtolower((string) $parameter) === 'null') {
                    $parameters[$key] = $this->getModel()->{$parameters[$key - 1]};
                }
            }
        }

        return 'unique:'.implode(',', $parameters);
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
