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
use Rinvex\Fort\Traits\CanVerifyEmail;
use Rinvex\Fort\Traits\CanVerifyPhone;
use Rinvex\Fort\Traits\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Rinvex\Fort\Traits\CanResetPassword;
use Illuminate\Database\Eloquent\SoftDeletes;
use Rinvex\Fort\Contracts\CanVerifyEmailContract;
use Rinvex\Fort\Contracts\CanVerifyPhoneContract;
use Rinvex\Fort\Contracts\AuthenticatableContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Rinvex\Fort\Contracts\CanResetPasswordContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract, CanVerifyEmailContract, CanVerifyPhoneContract
{
    use Notifiable, Authenticatable, Authorizable, CanResetPassword, CanVerifyEmail, CanVerifyPhone, HasRoles, SoftDeletes;

    /**
     * {@inheritdoc}
     */
    protected $dates = [
        'email_verified_at',
        'deleted_at',
        'birthdate',
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
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('rinvex.fort.tables.users'));
    }

    /**
     * A user may have multiple direct abilities.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function abilities()
    {
        return $this->belongsToMany(config('rinvex.fort.models.ability'), config('rinvex.fort.tables.ability_user'))
                    ->withTimestamps();
    }

    /**
     * A user may have multiple roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(config('rinvex.fort.models.role'), config('rinvex.fort.tables.role_user'))
                    ->withTimestamps();
    }

    /**
     * A user may have multiple persistences.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function persistences()
    {
        return $this->hasMany(config('rinvex.fort.models.persistence'));
    }

    /**
     * A user may have multiple socialites.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function socialites()
    {
        return $this->hasMany(config('rinvex.fort.models.socialite'));
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
        return $this->getAllAbilitiesAttribute()
                    ->where('resource', 'global')
                    ->where('policy', null)
                    ->contains('action', 'superadmin');
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
                'enabled'  => true,
                'authy_id' => $authyId,
            ]);

            $this->update(['two_factor' => $settings]);
        }

        return $authyId;
    }
}
