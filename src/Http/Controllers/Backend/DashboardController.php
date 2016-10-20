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

use DB;
use Carbon\Carbon;
use Rinvex\Fort\Http\Controllers\AuthenticatedController;

class DashboardController extends AuthenticatedController
{
    /**
     * Show the dashboard home.
     *
     * @return \Illuminate\Http\Response
     */
    public function home()
    {
        $abilityRepository = app('rinvex.fort.ability');
        $roleRepository    = app('rinvex.fort.role');
        $userRepository    = app('rinvex.fort.user');

        // Get recent registered users
        $limit = config('rinvex.fort.backend.items_per_dashboard');
        $users = $userRepository->orderBy('created_at', 'desc')->limit($limit)->findAll();

        // Get statistics
        $stats = [
            'abilities' => $abilityRepository->count(),
            'roles'     => $roleRepository->count(),
            'users'     => $userRepository->count(),
        ];

        // Get online users
        $onlineInterval = Carbon::now()->subMinutes(config('rinvex.fort.online.interval'));
        $persistences   = app('rinvex.fort.persistence')
            ->groupBy(['user_id'])
            ->with(['user'])
            ->where('attempt', '=', 0)
            ->where('updated_at', '>', $onlineInterval)
            ->findAll(['user_id', DB::raw('MAX(updated_at) as updated_at')]);

        return view('rinvex/fort::backend/dashboard.home', compact('users', 'persistences', 'stats'));
    }
}
