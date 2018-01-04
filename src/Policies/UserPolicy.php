<?php

declare(strict_types=1);

namespace Rinvex\Fort\Policies;

use Rinvex\Fort\Contracts\UserContract;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can list users.
     *
     * @param string                              $ability
     * @param \Rinvex\Fort\Contracts\UserContract $user
     *
     * @return bool
     */
    public function list($ability, UserContract $user)
    {
        return $user->allAbilities->pluck('slug')->contains($ability);
    }

    /**
     * Determine whether the user can create users.
     *
     * @param string                              $ability
     * @param \Rinvex\Fort\Contracts\UserContract $user
     *
     * @return bool
     */
    public function create($ability, UserContract $user)
    {
        return $user->allAbilities->pluck('slug')->contains($ability);
    }

    /**
     * Determine whether the user can update the user.
     *
     * @param string                              $ability
     * @param \Rinvex\Fort\Contracts\UserContract $user
     * @param \Rinvex\Fort\Contracts\UserContract $resource
     *
     * @return bool
     */
    public function update($ability, UserContract $user, UserContract $resource)
    {
        return $user->allAbilities->pluck('slug')->contains($ability)   // User can update users
               && ! $resource->isSuperadmin()                           // RESOURCE user is NOT superadmin
               && ! $resource->isProtected();                           // RESOURCE user is NOT protected
    }

    /**
     * Determine whether the user can delete the user.
     *
     * @param string                              $ability
     * @param \Rinvex\Fort\Contracts\UserContract $user
     * @param \Rinvex\Fort\Contracts\UserContract $resource
     *
     * @return bool
     */
    public function delete($ability, UserContract $user, UserContract $resource)
    {
        return $user->allAbilities->pluck('slug')->contains($ability)   // User can delete users
               && $resource->id !== $user->id                           // User can NOT delete himself
               && ! $resource->isSuperadmin()                           // RESOURCE user is NOT superadmin
               && ! $resource->isProtected();                           // RESOURCE user is NOT protected
    }
}
