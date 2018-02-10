<?php

declare(strict_types=1);

namespace Rinvex\Fort\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Rinvex\Fort\Models\Socialite.
 *
 * @property int                                                $id
 * @property int                                                $user_id
 * @property string                                             $user_type
 * @property string                                             $provider
 * @property string                                             $provider_uid
 * @property \Carbon\Carbon|null                                $created_at
 * @property \Carbon\Carbon|null                                $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Socialite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Socialite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Socialite whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Socialite whereProviderUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Socialite whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Socialite whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Socialite whereUserType($value)
 * @mixin \Eloquent
 */
class Socialite extends Model
{
    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'user_id',
        'user_type',
        'provider',
        'provider_uid',
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'user_id' => 'integer',
        'user_type' => 'string',
        'provider' => 'string',
        'provider_uid' => 'string',
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
     * Get the owning user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function user(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get socialites of the given user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model   $user
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfUser(Builder $builder, Model $user): Builder
    {
        return $builder->where('user_type', $user->getMorphClass())->where('user_id', $user->getKey());
    }
}
