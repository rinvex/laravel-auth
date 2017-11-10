<?php

declare(strict_types=1);

namespace Rinvex\Fort\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Rinvex\Fort\Contracts\SessionContract;

/**
 * Rinvex\Fort\Models\Session.
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
class Session extends Model implements SessionContract
{
    /**
     * {@inheritdoc}
     */
    public $incrementing = false;

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
    protected $casts = [
        'user_id' => 'integer',
        'ip_address' => 'string',
        'user_agent' => 'string',
        'payload' => 'string',
        'last_activity' => 'datetime',
    ];

    /**
     * {@inheritdoc}
     */
    public $timestamps = false;

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
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string                                $column
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMostRecent(Builder $builder, $column = 'last_activity'): Builder
    {
        return $builder->latest($column);
    }

    /**
     * Add an "order by" clause to retrieve least recent sessions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string                                $column
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLeastRecent(Builder $builder, $column = 'last_activity'): Builder
    {
        return $builder->oldest($column);
    }

    /**
     * Use joins to order by the users' column attributes.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string                                $column
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByUsers(Builder $builder, $column, $dir = 'ASC'): Builder
    {
        return $builder->join(config('rinvex.fort.tables.users'), config('session.table').'.user_id', '=', config('rinvex.fort.tables.users').'.id')->orderBy(config('rinvex.fort.tables.users').".{$column}", $dir);
    }

    /**
     * Constrain the query to retrieve only sessions of guests who
     * have been active within the specified number of seconds.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param int                                   $seconds
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGuestsBySeconds(Builder $builder, $seconds = 60): Builder
    {
        return $builder->where('last_activity', '>=', time() - $seconds)->whereNull('user_id');
    }

    /**
     * Alias for the `guestsByMinutes` query method.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param int                                   $minutes
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGuests(Builder $builder, $minutes = 5): Builder
    {
        return $builder->guestsByMinutes($minutes);
    }

    /**
     * Constrain the query to retrieve only sessions of guests who
     * have been active within the specified number of minutes.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param int                                   $minutes
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGuestsByMinutes(Builder $builder, $minutes = 5): Builder
    {
        return $builder->guestsBySeconds($minutes * 60);
    }

    /**
     * Constrain the query to retrieve only sessions of guests who
     * have been active within the specified number of hours.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param int                                   $hours
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGuestsByHours(Builder $builder, $hours = 1): Builder
    {
        return $builder->guestsByMinutes($hours * 60);
    }

    /**
     * Constrain the query to retrieve only sessions of users who
     * have been active within the specified number of seconds.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param int                                   $seconds
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsersBySeconds(Builder $builder, $seconds = 60): Builder
    {
        return $builder->with(['user'])->where('last_activity', '>=', Carbon::now()->subSeconds($seconds))->whereNotNull('user_id');
    }

    /**
     * Alias for the `usersByMinutes` query method.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param int                                   $minutes
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsers(Builder $builder, $minutes = 5): Builder
    {
        return $builder->usersByMinutes($minutes);
    }

    /**
     * Constrain the query to retrieve only sessions of users who
     * have been active within the specified number of minutes.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param int                                   $minutes
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsersByMinutes(Builder $builder, $minutes = 5): Builder
    {
        return $builder->usersBySeconds($minutes * 60);
    }

    /**
     * Constrain the query to retrieve only sessions of users who
     * have been active within the specified number of hours.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param int                                   $hours
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsersByHours(Builder $builder, $hours = 1): Builder
    {
        return $builder->usersByMinutes($hours * 60);
    }
}
