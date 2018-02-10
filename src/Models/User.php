<?php

declare(strict_types=1);

namespace Rinvex\Fort\Models;

use Rinvex\Country\Country;
use Rinvex\Language\Language;
use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Rinvex\Fort\Traits\HasHashables;
use Rinvex\Fort\Traits\CanVerifyEmail;
use Rinvex\Fort\Traits\CanVerifyPhone;
use Illuminate\Database\Eloquent\Model;
use Rinvex\Cacheable\CacheableEloquent;
use Illuminate\Notifications\Notifiable;
use Rinvex\Fort\Traits\CanResetPassword;
use Rinvex\Support\Traits\ValidatingTrait;
use Rinvex\Fort\Traits\AuthenticatableTwoFactor;
use Rinvex\Fort\Contracts\CanVerifyEmailContract;
use Rinvex\Fort\Contracts\CanVerifyPhoneContract;
use Silber\Bouncer\Database\HasRolesAndAbilities;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Rinvex\Fort\Contracts\CanResetPasswordContract;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Rinvex\Fort\Contracts\AuthenticatableTwoFactorContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthenticatableContract, AuthenticatableTwoFactorContract, AuthorizableContract, CanResetPasswordContract, CanVerifyEmailContract, CanVerifyPhoneContract
{
    use Notifiable;
    use Authorizable;
    use HasHashables;
    use CanVerifyEmail;
    use CanVerifyPhone;
    use Authenticatable;
    use ValidatingTrait;
    use CanResetPassword;
    use CacheableEloquent;
    use HasRolesAndAbilities;
    use AuthenticatableTwoFactor;

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
        'name_prefix',
        'first_name',
        'middle_name',
        'last_name',
        'name_suffix',
        'title',
        'country_code',
        'language_code',
        'birthday',
        'gender',
        'is_active',
        'last_activity',
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'username' => 'string',
        'password' => 'string',
        'two_factor' => 'json',
        'email' => 'string',
        'email_verified' => 'boolean',
        'email_verified_at' => 'datetime',
        'phone' => 'string',
        'phone_verified' => 'boolean',
        'phone_verified_at' => 'datetime',
        'name_prefix' => 'string',
        'first_name' => 'string',
        'middle_name' => 'string',
        'last_name' => 'string',
        'name_suffix' => 'string',
        'title' => 'string',
        'country_code' => 'string',
        'language_code' => 'string',
        'birthday' => 'string',
        'gender' => 'string',
        'is_active' => 'boolean',
        'last_activity' => 'datetime',
        'deleted_at' => 'datetime',
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
    protected $observables = [
        'validating',
        'validated',
    ];

    /**
     * The attributes to be encrypted before saving.
     *
     * @var array
     */
    protected $hashables = [
        'password',
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
     * Phone verification notification class.
     *
     * @var string
     */
    protected $passwordResetNotificationClass;

    /**
     * Email verification notification class.
     *
     * @var string
     */
    protected $emailVerificationNotificationClass;

    /**
     * Phone verification notification class.
     *
     * @var string
     */
    protected $phoneVerificationNotificationClass;

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
            'username' => 'required|alpha_dash|min:3|max:150|unique:'.config('rinvex.fort.tables.users').',username',
            'password' => 'sometimes|required|min:'.config('rinvex.fort.password_min_chars'),
            'two_factor' => 'nullable|array',
            'email' => 'required|email|min:3|max:150|unique:'.config('rinvex.fort.tables.users').',email',
            'email_verified' => 'sometimes|boolean',
            'email_verified_at' => 'nullable|date',
            'phone' => 'nullable|numeric|min:4',
            'phone_verified' => 'sometimes|boolean',
            'phone_verified_at' => 'nullable|date',
            'name_prefix' => 'nullable|string|max:150',
            'first_name' => 'nullable|string|max:150',
            'middle_name' => 'nullable|string|max:150',
            'last_name' => 'nullable|string|max:150',
            'name_suffix' => 'nullable|string|max:150',
            'title' => 'nullable|string|max:150',
            'country_code' => 'nullable|alpha|size:2|country',
            'language_code' => 'nullable|alpha|size:2|language',
            'birthday' => 'nullable|date_format:Y-m-d',
            'gender' => 'nullable|string|in:male,female',
            'is_active' => 'sometimes|boolean',
            'last_activity' => 'nullable|date',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function (self $user) {
            foreach (array_intersect($user->getHashables(), array_keys($user->getAttributes())) as $hashable) {
                if ($user->isDirty($hashable) && Hash::needsRehash($user->$hashable)) {
                    $user->$hashable = Hash::make($user->$hashable);
                }
            }
        });
    }

    /**
     * The user may have many sessions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function sessions(): MorphMany
    {
        return $this->morphMany(config('rinvex.fort.models.session'), 'user');
    }

    /**
     * The user may have many socialites.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function socialites(): MorphMany
    {
        return $this->morphMany(config('rinvex.fort.models.socialite'), 'user');
    }

    /**
     * Get name attribute.
     *
     * @return string
     */
    public function getNameAttribute(): string
    {
        $name = trim(implode(' ', [$this->name_prefix, $this->first_name, $this->middle_name, $this->last_name, $this->name_suffix]));

        return $name ?: $this->username;
    }

    /**
     * Route notifications for the authy channel.
     *
     * @return int|null
     */
    public function routeNotificationForAuthy(): ?int
    {
        if (! ($authyId = array_get($this->getTwoFactor(), 'phone.authy_id')) && $this->getEmailForVerification() && $this->getPhoneForVerification() && $this->getCountryForVerification()) {
            $result = app('rinvex.authy.user')->register($this->getEmailForVerification(), preg_replace('/[^0-9]/', '', $this->getPhoneForVerification()), $this->getCountryForVerification());
            $authyId = $result->get('user')['id'];

            // Prepare required variables
            $twoFactor = $this->getTwoFactor();

            // Update user account
            array_set($twoFactor, 'phone.authy_id', $authyId);

            $this->fill(['two_factor' => $twoFactor])->forceSave();
        }

        return $authyId;
    }

    /**
     * Get the user's country.
     *
     * @return \Rinvex\Country\Country
     */
    public function getCountryAttribute(): Country
    {
        return country($this->country_code);
    }

    /**
     * Get the user's language.
     *
     * @return \Rinvex\Language\Language
     */
    public function getLanguageAttribute(): Language
    {
        return language($this->language_code);
    }

    /**
     * Activate the user.
     *
     * @return $this
     */
    public function activate()
    {
        $this->update(['is_active' => true]);

        return $this;
    }

    /**
     * Deactivate the user.
     *
     * @return $this
     */
    public function deactivate()
    {
        $this->update(['is_active' => false]);

        return $this;
    }
}
