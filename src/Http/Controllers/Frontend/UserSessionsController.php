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

use Illuminate\Http\Request;
use Rinvex\Fort\Http\Controllers\AuthenticatedController;

class UserSessionsController extends AuthenticatedController
{
    /**
     * Show the account sessions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('rinvex/fort::frontend/user.sessions');
    }

    /**
     * Flush the given session.
     *
     * @param string|null $token
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function flush(Request $request, $token = null)
    {
        $status = '';

        if ($token) {
            app('rinvex.fort.persistence')->delete($token);
            $status = trans('rinvex/fort::frontend/messages.auth.session.flushed');
        } elseif (request()->get('confirm')) {
            app('rinvex.fort.persistence')->deleteByUser($request->user($this->getGuard())->id);
            $status = trans('rinvex/fort::frontend/messages.auth.session.flushedall');
        }

        return intend([
            'back' => true,
            'with' => ['warning' => $status],
        ]);
    }
}
