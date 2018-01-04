<?php

declare(strict_types=1);

namespace Rinvex\Fort\Contracts;

/**
 * Rinvex\Fort\Contracts\UserContract.
 *
 * @property int                                                                                                            $id
 * @property string                                                                                                         $username
 * @property string                                                                                                         $password
 * @property string|null                                                                                                    $remember_token
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
 * @property string                                                                                                         $birthday
 * @property string                                                                                                         $gender
 * @property bool                                                                                                           $is_active
 * @property \Carbon\Carbon                                                                                                 $last_activity
 * @property \Carbon\Carbon|null                                                                                            $created_at
 * @property \Carbon\Carbon|null                                                                                            $updated_at
 * @property \Carbon\Carbon|null                                                                                            $deleted_at
 * @property \Illuminate\Database\Eloquent\Collection|\Rinvex\Fort\Models\Ability[]                                         $abilities
 * @property-read \Illuminate\Support\Collection                                                                            $all_abilities
 * @property-read \Rinvex\Country\Country                                                                                   $country
 * @property-read \Rinvex\Language\Language                                                                                 $language
 * @property-read string                                                                                                    $name
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property \Illuminate\Database\Eloquent\Collection|\Rinvex\Fort\Models\Role[]                                            $roles
 * @property-read \Illuminate\Database\Eloquent\Collection|\Rinvex\Fort\Models\Session[]                                    $sessions
 * @property-read \Illuminate\Database\Eloquent\Collection|\Rinvex\Fort\Models\Socialite[]                                  $socialites
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\User role($roles)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\User whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\User whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\User whereEmailVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\User whereJobTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\User whereLanguageCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\User whereLastActivity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\User whereMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\User whereNamePrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\User whereNameSuffix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\User wherePhoneVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\User wherePhoneVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\User whereTwoFactor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\User whereUsername($value)
 * @mixin \Eloquent
 */
interface UserContract
{
    //
}
