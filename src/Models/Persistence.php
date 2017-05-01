<?php

declare(strict_types=1);

namespace Rinvex\Fort\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Rinvex\Fort\Models\Persistence.
 *
 * @property string                        $token
 * @property int                           $user_id
 * @property string                        $agent
 * @property string                        $ip
 * @property bool                          $attempt
 * @property \Carbon\Carbon                $created_at
 * @property \Carbon\Carbon                $updated_at
 * @property-read \Rinvex\Fort\Models\User $user
 *
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\Persistence whereAgent($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\Persistence whereAttempt($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\Persistence whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\Persistence whereIp($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\Persistence whereToken($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\Persistence whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Rinvex\Fort\Models\Persistence whereUserId($value)
 * @mixin \Eloquent
 */
class Persistence extends Model
{
    /**
     * {@inheritdoc}
     */
    protected $primaryKey = 'token';

    /**
     * {@inheritdoc}
     */
    public $incrementing = false;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'user_id',
        'token',
        'agent',
        'ip',
        'attempt',
    ];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('rinvex.fort.tables.persistences'));
    }

    /**
     * A persistence always belongs to a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        $userModel = config('auth.providers.'.config('auth.guards.'.config('auth.defaults.guard').'.provider').'.model');

        return $this->belongsTo($userModel, 'user_id', 'id');
    }
}
