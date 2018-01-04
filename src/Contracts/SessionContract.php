<?php

declare(strict_types=1);

namespace Rinvex\Fort\Contracts;

/**
 * Rinvex\Fort\Contracts\SessionContract.
 *
 * @property int                                $id
 * @property int                                $user_id
 * @property string                             $ip_address
 * @property string                             $user_agent
 * @property string                             $payload
 * @property \Carbon\Carbon                     $last_activity
 * @property-read \Rinvex\Fort\Models\User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Session guests($minutes = 5)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Session guestsByHours($hours = 1)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Session guestsByMinutes($minutes = 5)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Session guestsBySeconds($seconds = 60)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Session leastRecent($column = 'last_activity')
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Session mostRecent($column = 'last_activity')
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Session orderByUsers($column, $dir = 'ASC')
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Session users($minutes = 5)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Session usersByHours($hours = 1)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Session usersByMinutes($minutes = 5)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Session usersBySeconds($seconds = 60)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Session whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Session whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Session whereLastActivity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Session wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Session whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Session whereUserId($value)
 * @mixin \Eloquent
 */
interface SessionContract
{
    //
}
