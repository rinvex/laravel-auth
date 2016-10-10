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
use Rinvex\Fort\Http\Controllers\AuthorizedController;

class UserSessionsController extends AuthorizedController
{
    /**
     * Show the account sessions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('rinvex/fort::frontend.user.sessions');
    }

    /**
     * Flush the given session.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function flush($token = null)
    {
        $status = '';

        if ($token) {
            app('rinvex.fort.persistence')->delete($token);
            $status = trans('rinvex/fort::frontend/messages.auth.session.flushed');
        } elseif (request()->get('confirm')) {
            app('rinvex.fort.persistence')->deleteByUser(Auth::guard($this->getGuard())->user()->id);
            $status = trans('rinvex/fort::frontend/messages.auth.session.flushedall');
        }

        return intend([
            'back' => true,
            'with' => ['rinvex.fort.alert.warning' => $status],
        ]);
    }
}
