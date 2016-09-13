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

use Rinvex\Fort\Traits\CanVerifyEmail;
use Rinvex\Fort\Traits\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Rinvex\Fort\Traits\CanResetPassword;
use Illuminate\Database\Eloquent\SoftDeletes;
use Rinvex\Fort\Contracts\CanVerifyEmailContract;
use Rinvex\Fort\Contracts\AuthenticatableContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Rinvex\Fort\Contracts\CanResetPasswordContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract, CanVerifyEmailContract
{
    use Notifiable, Authenticatable, Authorizable, CanResetPassword, CanVerifyEmail, SoftDeletes;

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
        'sufix',
        'job_title',
        'country',
        'birthdate',
        'gender',
        'moderated',
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
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     *
     * @return void
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
}
