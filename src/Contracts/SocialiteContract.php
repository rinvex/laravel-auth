<?php

declare(strict_types=1);

namespace Rinvex\Fort\Contracts;

/**
 * Rinvex\Fort\Contracts\SocialiteContract.
 *
 * @property int                           $id
 * @property int                           $user_id
 * @property string                        $provider
 * @property int                           $provider_uid
 * @property \Carbon\Carbon|null           $created_at
 * @property \Carbon\Carbon|null           $updated_at
 * @property-read \Rinvex\Fort\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Socialite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Socialite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Socialite whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Socialite whereProviderUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Socialite whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Socialite whereUserId($value)
 * @mixin \Eloquent
 */
interface SocialiteContract
{
    //
}
