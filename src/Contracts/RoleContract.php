<?php

declare(strict_types=1);

namespace Rinvex\Fort\Contracts;

/**
 * Rinvex\Fort\Contracts\RoleContract.
 *
 * @property int                                                                    $id
 * @property string                                                                 $slug
 * @property array                                                                  $name
 * @property array                                                                  $description
 * @property \Carbon\Carbon|null                                                    $created_at
 * @property \Carbon\Carbon|null                                                    $updated_at
 * @property \Carbon\Carbon|null                                                    $deleted_at
 * @property \Illuminate\Database\Eloquent\Collection|\Rinvex\Fort\Models\Ability[] $abilities
 * @property \Illuminate\Database\Eloquent\Collection|\Rinvex\Fort\Models\User[]    $users
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Role whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Role whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Role whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Role whereUpdatedAt($value)
 * @mixin \Eloquent
 */
interface RoleContract
{
    //
}
