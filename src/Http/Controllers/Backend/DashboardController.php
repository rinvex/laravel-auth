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

namespace Rinvex\Fort\Http\Controllers\Backend;

use Carbon\Carbon;
use Rinvex\Fort\Models\Role;
use Rinvex\Fort\Models\User;
use Rinvex\Fort\Models\Ability;
use Illuminate\Support\Facades\DB;
use Rinvex\Fort\Models\Persistence;
use Rinvex\Fort\Http\Controllers\AuthorizedController;

class DashboardController extends AuthorizedController
{
    /**
     * {@inheritdoc}
     */
    protected $resource = 'dashboard';

    /**
     * {@inheritdoc}
     */
    protected $resourceAbilityMap = ['home'   => 'access'];

    /**
     * Show the dashboard home.
     *
     * @return \Illuminate\Http\Response
     */
    public function home()
    {
        // Get recent registered users
        $limit = config('rinvex.fort.backend.items_per_dashboard');
        $users = User::orderBy('created_at', 'desc')->limit($limit)->get();

        // Get statistics
        $stats = [
            'abilities' => Ability::count(),
            'roles'     => Role::count(),
            'users'     => User::count(),
        ];

        // Get online users
        $onlineInterval = Carbon::now()->subMinutes(config('rinvex.fort.online.interval'));
        $persistences = Persistence::groupBy(['user_id'])
            ->with(['user'])
            ->where('attempt', '=', 0)
            ->where('updated_at', '>', $onlineInterval)
            ->get(['user_id', DB::raw('MAX(updated_at) as updated_at')]);

        return view('rinvex/fort::backend/dashboard.home', compact('users', 'persistences', 'stats'));
    }
}
