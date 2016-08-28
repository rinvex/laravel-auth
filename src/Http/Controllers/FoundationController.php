<?php

/*
 * NOTICE OF LICENSE
 *
 * Part of the Rinvex Fort Package.
 *
 * This source file is subject to The MIT License (MIT)
 * that is bundled with this package in the LICENSE file.
 *
 * Package: Rinvex Fort Package
 * License: The MIT License (MIT)
 * Link:    https://rinvex.com
 */

namespace Rinvex\Fort\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Rinvex\Fort\Traits\GetsMiddleware;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FoundationController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, GetsMiddleware;

    /**
     * The currently logged in user instance.
     *
     * @var \Rinvex\Fort\Contracts\AuthenticatableContract
     */
    protected $currentUser;

    /**
     * The password broker.
     *
     * @var string
     */
    protected $broker;

    /**
     * Create a new basic controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Get currently logged in user instance, or user instance of current Two-Factor login attempt
        $this->currentUser = Auth::guard($this->getGuard())->user() ?: Auth::guard($this->getGuard())->attemptUser();

        view()->share(['currentUser' => $this->currentUser]);
    }

    /**
     * Get the broker to be used.
     *
     * @return string
     */
    protected function getBroker()
    {
        return $this->broker;
    }
}
