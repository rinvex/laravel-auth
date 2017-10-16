<?php

declare(strict_types=1);

namespace Rinvex\Fort\Contracts;

/**
 * Rinvex\Fort\Contracts\AbilityContract.
 *
 * @property int                                                                      $id
 * @property string                                                                   $action
 * @property string                                                                   $resource
 * @property string                                                                   $policy
 * @property array                                                                    $name
 * @property array                                                                    $description
 * @property \Carbon\Carbon|null                                                      $created_at
 * @property \Carbon\Carbon|null                                                      $updated_at
 * @property \Carbon\Carbon|null                                                      $deleted_at
 * @property-read string                                                              $slug
 * @property \Illuminate\Database\Eloquent\Collection|\Rinvex\Fort\Models\Role[]      $roles
 * @property-read \Illuminate\Database\Eloquent\Collection|\Rinvex\Fort\Models\User[] $users
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Ability whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Ability whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Ability whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Ability whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Ability whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Ability whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Ability wherePolicy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Ability whereResource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Rinvex\Fort\Models\Ability whereUpdatedAt($value)
 * @mixin \Eloquent
 */
interface AbilityContract
{
    //
}
