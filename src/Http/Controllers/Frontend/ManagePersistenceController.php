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

namespace Rinvex\Fort\Http\Controllers\Frontend;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Rinvex\Fort\Contracts\UserRepositoryContract;
use Rinvex\Fort\Http\Controllers\AbstractController;

class ManagePersistenceController extends AbstractController
{
    /**
     * Create a new account controller instance.
     *
     * @param \Rinvex\Fort\Contracts\UserRepositoryContract $users
     *
     * @return void
     */
    public function __construct(UserRepositoryContract $users)
    {
        $this->middleware($this->getAuthMiddleware(), ['except' => $this->middlewareWhitelist]);
    }

    /**
     * Show the account sessions.
     *
     * @return \Illuminate\Http\Response
     */
    public function showPersistence()
    {
        return view('rinvex.fort::frontend.profile.persistence');
    }

    /**
     * Flush the given session.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function processPersistenceFlush($token = null)
    {
        $status = '';

        if ($token) {
            app('rinvex.fort.persistence')->delete($token);
            $status = Lang::get('rinvex.fort::message.auth.session.flushed');
        } elseif (request()->get('confirm')) {
            app('rinvex.fort.persistence')->deleteByUser(Auth::guard($this->getGuard())->user()->id);
            $status = Lang::get('rinvex.fort::message.auth.session.flushedall');
        }

        return intend([
            'back' => true,
            'with' => ['rinvex.fort.alert.warning' => $status],
        ]);
    }
}
