<?php

declare(strict_types=1);

namespace Rinvex\Fort\Models;

use Rinvex\Fort\Traits\HasRoles;
use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Rinvex\Fort\Traits\CanVerifyEmail;
use Rinvex\Fort\Traits\CanVerifyPhone;
use Watson\Validating\ValidatingTrait;
use Illuminate\Database\Eloquent\Model;
use Rinvex\Cacheable\CacheableEloquent;
use Rinvex\Support\Traits\HasHashables;
use Illuminate\Notifications\Notifiable;
use Rinvex\Fort\Traits\CanResetPassword;
use Rinvex\Fort\Traits\AuthenticatableTwoFactor;
use Rinvex\Fort\Contracts\CanVerifyEmailContract;
use Rinvex\Fort\Contracts\CanVerifyPhoneContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Rinvex\Fort\Contracts\CanResetPasswordContract;
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
 * @property \Carbon\Carbon                                                                                                 $phone_verified_at
 * @property string                                                                                                         $name_prefix
 * @property string                                                                                                         $first_name
 * @property string                                                                                                         $middle_name
 * @property string                                                                                                         $last_name
 * @property string                                                                                                         $name_suffix
 * @property string                                                                                                         $job_title
 * @property string                                                                                                         $country_code
 * @property string                                                                                                         $language_code
 * @property array                                                                                                          $two_factor
 * @property \Carbon\Carbon                                                                                                 $birthday
 * @property string                                                                                                         $gender
 * @property string                                                                                                         $website
 * @property string                                                                                                         $twitter
 * @property string                                                                                                         $facebook
 * @property string                                                                                                         $linkedin
 * @property string                                                                                                         $google_plus
 * @property string                                                                                                         $skype
 * @property bool                                                                                                           $active
 * @property \Carbon\Carbon                                                                                                 $login_at
 * @property \Carbon\Carbon                                                                                                 $created_at
 * @property \Carbon\Carbon                                                                                                 $updated_at
 * @property \Carbon\Carbon                                                                                                 $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Rinvex\Fort\Models\Ability[]                                    $abilities
 * @property-read array                                                                                                     $ability_list
 * @property-read \Illuminate\Support\Collection                                                                            $all_abilities
 * @property-read \Rinvex\Country\Country                                                                                   $country
 * @property-read \Rinvex\Language\Language                                                                                 $language
 * @property-read string                                                                                                    $name
 * @property-read array                                                                                                     $role_list
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\Rinvex\Fort\Models\Persistence[]                                $persistences
 * @property-read \Illuminate\Database\Eloquent\Collection|\Rinvex\Fort\Models\Role[]                                       $roles
 * @property-read \Illuminate\Database\Eloquent\Collection|\Rinvex\Fort\Models\Socialite[]                                  $socialites
 *
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User role($roles)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereActive($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereBirthday($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereCountryCode($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereEmailVerified($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereFacebook($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereFirstName($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereGender($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereGooglePlus($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereJobTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereLanguageCode($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereLastName($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereLinkedin($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereLoginAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereMiddleName($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereNamePrefix($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereNameSuffix($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User wherePhone($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User wherePhoneVerified($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User wherePhoneVerifiedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereSkype($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereTwitter($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereTwoFactor($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereUsername($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\User whereWebsite($value)
 * @mixin \Eloquent
 */
class User extends Model implements AuthenticatableContract, AuthenticatableTwoFactorContract, AuthorizableContract, CanResetPasswordContract, CanVerifyEmailContract, CanVerifyPhoneContract
{
    use HasRoles;
    use Notifiable;
    use Authorizable;
    use HasHashables;
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
        'birthday',
        'login_at',
        'deleted_at',
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
        'name_prefix',
        'first_name',
        'middle_name',
        'last_name',
        'name_suffix',
        'job_title',
        'country_code',
        'language_code',
        'birthday',
        'website',
        'twitter',
        'facebook',
        'linkedin',
        'google_plus',
        'skype',
        'gender',
        'active',
        'login_at',
        'abilities',
        'roles',
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
     * The attributes to be encrypted before saving.
     *
     * @var array
     */
    protected $hashables = ['password'];

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

        $this->setTable(config('rinvex.fort.tables.users'));
        $this->setRules([
            'email' => 'required|email|min:3|max:250|unique:'.config('rinvex.fort.tables.users').',email',
            'username' => 'required|alpha_dash|min:3|max:250|unique:'.config('rinvex.fort.tables.users').',username',
            'password' => 'sometimes|required|min:'.config('rinvex.fort.password_min_chars'),
            'gender' => 'nullable|string|in:male,female',
            'phone' => 'nullable|string',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function boot(): void
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
     * Register a validating user event with the dispatcher.
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
     * Register a validated user event with the dispatcher.
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
        $segments = [$this->name_prefix, $this->first_name, $this->middle_name, $this->last_name, $this->name_suffix];

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
            array_set($settings, 'phone.authy_id', $authyId);

            $this->fill(['two_factor' => $settings])->forceSave();
        }

        return $authyId;
    }

    /**
     * Get the user's country.
     *
     * @return \Rinvex\Country\Country
     */
    public function getCountryAttribute()
    {
        return country($this->country_code);
    }

    /**
     * Get the user's language.
     *
     * @return \Rinvex\Language\Language
     */
    public function getLanguageAttribute()
    {
        return language($this->language_code);
    }

    /**
     * Attach the user roles.
     *
     * @param array $roles
     *
     * @return void
     */
    public function setRolesAttribute(array $roles)
    {
        static::saved(function (self $model) use ($roles) {
            $model->roles()->syncWithoutDetaching($roles);
        });
    }

    /**
     * Attach the user abilities.
     *
     * @param array $abilities
     *
     * @return void
     */
    public function setAbilitiesAttribute(array $abilities)
    {
        static::saved(function (self $model) use ($abilities) {
            $model->abilities()->syncWithoutDetaching($abilities);
        });
    }
}
