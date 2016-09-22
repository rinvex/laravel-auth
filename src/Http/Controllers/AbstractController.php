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
use Rinvex\Fort\Traits\GetsMiddleware;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class AbstractController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, GetsMiddleware;

    /**
     * Resource Ability Map.
     *
     * Array of resource ability map.
     *
     * @var array
     */
    protected $resourceAbilityMap = [];

    /**
     * Whitelisted methods.
     *
     * Array of whitelisted methods which do not need to go through middleware.
     *
     * @var array
     */
    protected $middlewareWhitelist = [];

    /**
     * The password broker.
     *
     * @var string
     */
    protected $broker;

    /**
     * Get the broker to be used.
     *
     * @return string
     */
    protected function getBroker()
    {
        return $this->broker;
    }

    /**
     * Get the map of resource methods to ability names.
     *
     * @return array
     */
    protected function resourceAbilityMap()
    {
        return $this->resourceAbilityMap + [
            'show'    => 'view',
            'index'   => 'view',
            'import'  => 'import',
            'export'  => 'export',
            'copy'    => 'copy',
            'create'  => 'create',
            'store'   => 'create',
            'edit'    => 'update',
            'update'  => 'update',
            'destroy' => 'delete',
        ];
    }
}
