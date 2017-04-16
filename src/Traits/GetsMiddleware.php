<?php

declare(strict_types=1);

namespace Rinvex\Fort\Traits;

trait GetsMiddleware
{
    /**
     * The authentication guard.
     *
     * @var string
     */
    protected $guard;

    /**
     * Get the guard to be used during authentication.
     *
     * @return string|null
     */
    protected function getGuard()
    {
        return $this->guard;
    }

    /**
     * Get the guest middleware for the application.
     */
    protected function getGuestMiddleware()
    {
        return ($guard = $this->getGuard()) ? 'guest:'.$guard : 'guest';
    }

    /**
     * Get the auth middleware for the application.
     *
     * @return string
     */
    protected function getAuthMiddleware()
    {
        return ($guard = $this->getGuard()) ? 'auth:'.$guard : 'auth';
    }
}
