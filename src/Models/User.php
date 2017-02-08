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

use Rinvex\Fort\Traits\HasRoles;
use Illuminate\Auth\Authenticatable;
use Watson\Validating\ValidatingTrait;
use Rinvex\Fort\Traits\CanVerifyEmail;
use Rinvex\Fort\Traits\CanVerifyPhone;
use Rinvex\Cacheable\CacheableEloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Rinvex\Fort\Traits\CanResetPassword;
use Rinvex\Fort\Traits\AuthenticatableTwoFactor;
use Rinvex\Fort\Contracts\CanVerifyEmailContract;
use Rinvex\Fort\Contracts\CanVerifyPhoneContract;
use Rinvex\Fort\Contracts\CanResetPasswordContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Rinvex\Fort\Contracts\AuthenticatableTwoFactorContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

/**
 * Rinvex\Fort\Models\User.
 *
 * @property int                                                                                                            $id
 * @property string                                                                                                         $username
 * @property string                                                                                                         $password
 * @property string                                                                                                         $remember_token
 * @property string                                                                                                         $email
 * @property bool                                                                                                           $email_verified
 * @property \Carbon\Carbon                                                                                                 $email_verified_at
 * @property string                                                                                                         $phone
 * @property bool                                                                                                           $phone_verified
 * @property string                                                                                                         $phone_verified_at
 * @property string                                                                                                         $prefix
 * @property string                                                                                                         $first_name
 * @property string                                                                                                         $middle_name
 * @property string                                                                                                         $last_name
 * @property string                                                                                                         $sufix
 * @property string                                                                                                         $job_title
 * @property string                                                                                                         $country
 * @property array                                                                                                          $two_factor
 * @property \Carbon\Carbon                                                                                                 $birthdate
 * @property string                                                                                                         $gender
 * @property bool                                                                                                           $active
 * @property \Carbon\Carbon                                                                                                 $login_at
 * @property \Carbon\Carbon                                                                                                 $created_at
 * @property \Carbon\Carbon                                                                                                 $updated_at
 * @property \Carbon\Carbon                                                                                                 $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Rinvex\Fort\Models\Ability[]                                    $abilities
 * @property-read \Illuminate\Database\Eloquent\Collection|\Rinvex\Fort\Models\Role[]                                       $roles
 * @property-read \Illuminate\Database\Eloquent\Collection|\Rinvex\Fort\Models\Persistence[]                                $persistences
 * @property-read \Illuminate\Database\Eloquent\Collection|\Rinvex\Fort\Models\Socialite[]                                  $socialites
 * @property-read string                                                                                                    $name
 * @property-read \Illuminate\Support\Collection                                                                            $all_abilities
 * @property-read array                                                                                                     $ability_list
 * @property-read array                                                                                                     $role_list
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $readNotifications
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $unreadNotifications
 *
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereUsername($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereEmailVerified($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User wherePhone($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User wherePhoneVerified($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User wherePhoneVerifiedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User wherePrefix($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereFirstName($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereMiddleName($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereLastName($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereSufix($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereJobTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereCountry($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereTwoFactor($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereBirthdate($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereGender($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereActive($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereLoginAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereDeletedAt($value)
 * @mixin \Illuminate\Database\Eloquent\Model
 */
class User extends Model implements AuthenticatableContract, AuthenticatableTwoFactorContract, AuthorizableContract, CanResetPasswordContract, CanVerifyEmailContract, CanVerifyPhoneContract
{
    use HasRoles;
    use Notifiable;
    use Authorizable;
    use CanVerifyEmail;
    use CanVerifyPhone;
    use Authenticatable;
    use ValidatingTrait;
    use CanResetPassword;
    use CacheableEloquent;
    use AuthenticatableTwoFactor;

    /**
     * {@inheritdoc}
     */
    protected $dates = [
        'email_verified_at',
        'phone_verified_at',
        'login_at',
    ];

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'username',
        'password',
        'two_factor',
        'email',
        'email_verified',
        'email_verified_at',
        'phone',
        'phone_verified',
        'phone_verified_at',
        'prefix',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'job_title',
        'country',
        'birthdate',
        'gender',
        'active',
        'login_at',
    ];

    /**
     * {@inheritdoc}
     */
    protected $hidden = [
        'password',
        'two_factor',
        'remember_token',
    ];

    /**
     * {@inheritdoc}
     */
    protected $with = ['abilities', 'roles'];

    /**
     * {@inheritdoc}
     */
    protected $observables = ['validating', 'validated'];

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

        $this->setTable(config('rinvex.fort.tables.users'));
        $this->setRules([
            'username' => 'required|alpha_dash|max:255|unique:'.config('rinvex.fort.tables.users').',username',
            'email' => 'required|email|max:255|unique:'.config('rinvex.fort.tables.users').',email',
            'password' => 'sometimes|required|min:'.config('rinvex.fort.passwordreset.minimum_characters'),
            'gender' => 'in:male,female,undisclosed',
            'active' => 'boolean',
            'email_verified' => 'boolean',
            'phone_verified' => 'boolean',
        ]);
    }

    /**
     * A user may have multiple direct abilities.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function abilities()
    {
        return $this->belongsToMany(config('rinvex.fort.models.ability'), config('rinvex.fort.tables.ability_user'), 'user_id', 'ability_id')
                    ->withTimestamps();
    }

    /**
     * A user may have multiple roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(config('rinvex.fort.models.role'), config('rinvex.fort.tables.role_user'), 'user_id', 'role_id')
                    ->withTimestamps();
    }

    /**
     * A user may have multiple persistences.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function persistences()
    {
        return $this->hasMany(config('rinvex.fort.models.persistence'), 'user_id', 'id');
    }

    /**
     * A user may have multiple socialites.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function socialites()
    {
        return $this->hasMany(config('rinvex.fort.models.socialite'), 'user_id', 'id');
    }

    /**
     * Get name attribute.
     *
     * @return string
     */
    public function getNameAttribute()
    {
        $segments = [$this->prefix, $this->first_name, $this->middle_name, $this->last_name, $this->suffix];

        return trim(implode(' ', $segments));
    }

    /**
     * Get all abilities of the user.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllAbilitiesAttribute()
    {
        return $this->abilities->merge($this->roles->pluck('abilities')->collapse());
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
     * Get the list of role Ids.
     *
     * @return array
     */
    public function getRoleListAttribute()
    {
        return $this->roles->pluck('id')->toArray();
    }

    /**
     * Determine if the user is super admin.
     *
     * @return bool
     */
    public function isSuperadmin()
    {
        return $this->getAllAbilitiesAttribute()->where('resource', 'global')->where('policy', null)->contains('action', 'superadmin');
    }

    /**
     * Determine if the user is protected.
     *
     * @return bool
     */
    public function isProtected()
    {
        return in_array($this->id, config('rinvex.fort.protected.users'));
    }

    /**
     * Set the user's password.
     *
     * @param string $value
     *
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    /**
     * Route notifications for the authy channel.
     *
     * @return int
     */
    public function routeNotificationForAuthy()
    {
        if (! $authyId = array_get($this->getTwoFactor(), 'phone.authy_id')) {
            $result = app('rinvex.authy.user')->register($this->getEmailForVerification(), preg_replace('/[^0-9]/', '', $this->getPhoneForVerification()), $this->getCountryForVerification());
            $authyId = $result->get('user')['id'];

            // Prepare required variables
            $settings = $this->getTwoFactor();

            // Update user account
            array_set($settings, 'phone', [
                'enabled' => true,
                'authy_id' => $authyId,
            ]);

            $this->update(['two_factor' => $settings]);
        }

        return $authyId;
    }
}
