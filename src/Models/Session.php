<?php

declare(strict_types=1);

namespace Rinvex\Fort\Models;

use Illuminate\Database\Eloquent\Model;

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
     * @param  \Illuminate\Database\Query\Builder $query
     * @param  string                             $column
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeMostRecent($query, $column = 'last_activity')
    {
        return $query->latest($column);
    }

    /**
     * Add an "order by" clause to retrieve least recent sessions.
     *
     * @param  \Illuminate\Database\Query\Builder $query
     * @param  string                             $column
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeLeastRecent($query, $column = 'last_activity')
    {
        return $query->oldest($column);
    }

    /**
     * Use joins to order by the users' column attributes.
     *
     * @param  \Illuminate\Database\Query\Builder $query
     * @param  string                             $column
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeOrderByUsers($query, $column, $dir = 'ASC')
    {
        $table = $this->getTable();

        $userModel = config('auth.providers.'.config('auth.guards.'.config('auth.defaults.guard').'.provider').'.model');
        $user = new $userModel;
        $userTable = $user->getTable();
        $userKey = $user->getKeyName();

        return $query->join($userTable, "{$table}.user_id", '=', "{$userTable}.{$userKey}")->orderBy("{$userTable}.{$column}", $dir);
    }

    /**
     * Constrain the query to retrieve only sessions of guests who
     * have been active within the specified number of seconds.
     *
     * @param  \Illuminate\Database\Query\Builder $query
     * @param  int                                $seconds
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeGuestsBySeconds($query, $seconds = 60)
    {
        return $query->where('last_activity', '>=', time() - $seconds)->whereNull('user_id');
    }

    /**
     * Alias for the `guestsByMinutes` query method.
     *
     * @param  \Illuminate\Database\Query\Builder $query
     * @param  int                                $minutes
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeGuests($query, $minutes = 5)
    {
        return $query->guestsByMinutes($minutes);
    }

    /**
     * Constrain the query to retrieve only sessions of guests who
     * have been active within the specified number of minutes.
     *
     * @param  \Illuminate\Database\Query\Builder $query
     * @param  int                                $minutes
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeGuestsByMinutes($query, $minutes = 5)
    {
        return $query->guestsBySeconds($minutes * 60);
    }

    /**
     * Constrain the query to retrieve only sessions of guests who
     * have been active within the specified number of hours.
     *
     * @param  \Illuminate\Database\Query\Builder $query
     * @param  int                                $hours
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeGuestsByHours($query, $hours = 1)
    {
        return $query->guestsByMinutes($hours * 60);
    }

    /**
     * Constrain the query to retrieve only sessions of users who
     * have been active within the specified number of seconds.
     *
     * @param  \Illuminate\Database\Query\Builder $query
     * @param  int                                $seconds
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeUsersBySeconds($query, $seconds = 60)
    {
        return $query->with(['user'])->where('last_activity', '>=', time() - $seconds)->whereNotNull('user_id');
    }

    /**
     * Alias for the `usersByMinutes` query method.
     *
     * @param  \Illuminate\Database\Query\Builder $query
     * @param  int                                $minutes
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeUsers($query, $minutes = 5)
    {
        return $query->usersByMinutes($minutes);
    }

    /**
     * Constrain the query to retrieve only sessions of users who
     * have been active within the specified number of minutes.
     *
     * @param  \Illuminate\Database\Query\Builder $query
     * @param  int                                $minutes
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeUsersByMinutes($query, $minutes = 5)
    {
        return $query->usersBySeconds($minutes * 60);
    }

    /**
     * Constrain the query to retrieve only sessions of users who
     * have been active within the specified number of hours.
     *
     * @param  \Illuminate\Database\Query\Builder $query
     * @param  int                                $hours
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeUsersByHours($query, $hours = 1)
    {
        return $query->usersByMinutes($hours * 60);
    }
}
