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

use Watson\Validating\ValidatingTrait;
use Rinvex\Cacheable\CacheableEloquent;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * Rinvex\Fort\Models\Ability.
 *
 * @property int                                                                      $id
 * @property string                                                                   $action
 * @property string                                                                   $resource
 * @property string                                                                   $policy
 * @property string                                                                   $name
 * @property string                                                                   $description
 * @property \Carbon\Carbon                                                           $created_at
 * @property \Carbon\Carbon                                                           $updated_at
 * @property \Carbon\Carbon                                                           $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Rinvex\Fort\Models\Role[] $roles
 * @property-read \Illuminate\Database\Eloquent\Collection|\Rinvex\Fort\Models\User[] $users
 * @property-read bool                                                                $slug
 *
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\Ability whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\Ability whereAction($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\Ability whereResource($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\Ability wherePolicy($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\Ability whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\Ability whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\Ability whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\Ability whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\Ability whereDeletedAt($value)
 * @mixin \Illuminate\Database\Eloquent\Model
 */
class Ability extends Model
{
    use HasTranslations;
    use ValidatingTrait;
    use CacheableEloquent;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'action',
        'resource',
        'policy',
        'name',
        'description',
    ];

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
     * {@inheritdoc}
     */
    protected $validationMessages = [
        'action.unique' => 'The combination of (action & resource) fields has already been taken.',
        'resource.unique' => 'The combination of (action & resource) fields has already been taken.',
    ];

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

        $this->setTable(config('rinvex.fort.tables.abilities'));
        $this->addObservableEvents(['attaching', 'attached', 'detaching', 'detached']);
        $this->setRules([
            'name' => 'required',
            'action' => 'required|unique:'.config('rinvex.fort.tables.abilities').',action,NULL,id,resource,'.$this->resource,
            'resource' => 'required|unique:'.config('rinvex.fort.tables.abilities').',resource,NULL,id,action,'.$this->action,
        ]);
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
        return $this->belongsToMany(config('rinvex.fort.models.user'), config('rinvex.fort.tables.ability_user'), 'ability_id', 'user_id')
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
     * @return bool
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
        if ((strpos($parameters[0], '.') === false) && (($connectionName = $this->getModel()->getConnectionName()) !== null)) {
            $parameters[0] = $connectionName.'.'.$parameters[0];
        }

        // If the field name isn't get, infer it.
        if (! isset($parameters[1])) {
            $parameters[1] = $field;
        }

        if ($this->exists) {
            // If the identifier isn't set, infer it.
            if (! isset($parameters[2]) || strtolower($parameters[2]) === 'null') {
                $parameters[2] = $this->getModel()->getKey();
            }

            // If the primary key isn't set, infer it.
            if (! isset($parameters[3])) {
                $parameters[3] = $this->getModel()->getKeyName();
            }

            foreach ($parameters as $key => $parameter) {
                if (strtolower($parameter) === 'null') {
                    $parameters[$key] = $this->getModel()->{$parameters[$key - 1]};
                }
            }
        }

        return 'unique:'.implode(',', $parameters);
    }
}
