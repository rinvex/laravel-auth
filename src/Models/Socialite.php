<?php

declare(strict_types=1);

namespace Rinvex\Fort\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Rinvex\Fort\Models\Socialite.
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
class Socialite extends Model
{
    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'user_id',
        'provider',
        'provider_uid',
    ];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('rinvex.fort.tables.socialites'));
    }

    /**
     * A socialite always belongs to a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        $userModel = config('auth.providers.'.config('auth.guards.'.config('auth.defaults.guard').'.provider').'.model');

        return $this->belongsTo($userModel, 'user_id', 'id');
    }
}
