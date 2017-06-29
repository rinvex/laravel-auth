<?php

declare(strict_types=1);

namespace Rinvex\Fort\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Rinvex\Fort\Models\Session.
 *
 * @property int                                $id
 * @property int|null                           $user_id
 * @property string|null                        $ip_address
 * @property string|null                        $user_agent
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
class Session extends Model
{
    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'id',
        'user_id',
        'ip_address',
        'user_agent',
        'payload',
        'last_activity',
    ];

    /**
     * {@inheritdoc}
     */
    public $timestamps = false;

    /**
     * {@inheritdoc}
     */
    protected $dates = ['last_activity'];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('session.table'));
    }

    /**
     * A session always belongs to a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        $userModel = config('auth.providers.'.config('auth.guards.'.config('auth.defaults.guard').'.provider').'.model');

        return $this->belongsTo($userModel, 'user_id', 'id');
    }

    /**
     * Add an "order by" clause to retrieve most recent sessions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string                                $column
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMostRecent(Builder $query, $column = 'last_activity')
    {
        return $query->latest($column);
    }

    /**
     * Add an "order by" clause to retrieve least recent sessions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string                                $column
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLeastRecent(Builder $query, $column = 'last_activity')
    {
        return $query->oldest($column);
    }

    /**
     * Use joins to order by the users' column attributes.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string                                $column
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByUsers(Builder $query, $column, $dir = 'ASC')
    {
        $table = $this->getTable();

        $userModel = config('auth.providers.'.config('auth.guards.'.config('auth.defaults.guard').'.provider').'.model');
        $user = new $userModel();
        $userTable = $user->getTable();
        $userKey = $user->getKeyName();

        return $query->join($userTable, "{$table}.user_id", '=', "{$userTable}.{$userKey}")->orderBy("{$userTable}.{$column}", $dir);
    }

    /**
     * Constrain the query to retrieve only sessions of guests who
     * have been active within the specified number of seconds.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int                                   $seconds
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGuestsBySeconds(Builder $query, $seconds = 60)
    {
        return $query->where('last_activity', '>=', time() - $seconds)->whereNull('user_id');
    }

    /**
     * Alias for the `guestsByMinutes` query method.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int                                   $minutes
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGuests(Builder $query, $minutes = 5)
    {
        return $query->guestsByMinutes($minutes);
    }

    /**
     * Constrain the query to retrieve only sessions of guests who
     * have been active within the specified number of minutes.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int                                   $minutes
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGuestsByMinutes(Builder $query, $minutes = 5)
    {
        return $query->guestsBySeconds($minutes * 60);
    }

    /**
     * Constrain the query to retrieve only sessions of guests who
     * have been active within the specified number of hours.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int                                   $hours
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGuestsByHours(Builder $query, $hours = 1)
    {
        return $query->guestsByMinutes($hours * 60);
    }

    /**
     * Constrain the query to retrieve only sessions of users who
     * have been active within the specified number of seconds.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int                                   $seconds
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsersBySeconds(Builder $query, $seconds = 60)
    {
        return $query->with(['user'])->where('last_activity', '>=', time() - $seconds)->whereNotNull('user_id');
    }

    /**
     * Alias for the `usersByMinutes` query method.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int                                   $minutes
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsers(Builder $query, $minutes = 5)
    {
        return $query->usersByMinutes($minutes);
    }

    /**
     * Constrain the query to retrieve only sessions of users who
     * have been active within the specified number of minutes.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int                                   $minutes
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsersByMinutes(Builder $query, $minutes = 5)
    {
        return $query->usersBySeconds($minutes * 60);
    }

    /**
     * Constrain the query to retrieve only sessions of users who
     * have been active within the specified number of hours.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int                                   $hours
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsersByHours(Builder $query, $hours = 1)
    {
        return $query->usersByMinutes($hours * 60);
    }
}
