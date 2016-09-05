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

use Illuminate\Support\Facades\Lang;
use Rinvex\Fort\Contracts\ResetBrokerContract;
use Rinvex\Fort\Http\Requests\PasswordResetRequest;
use Rinvex\Fort\Http\Controllers\AbstractController;

class ForgotPasswordController extends AbstractController
{
    /**
     * Create a new reset password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware($this->getGuestMiddleware(), ['except' => $this->middlewareWhitelist]);
    }

    /**
     * Show the password reset request form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showForgotPassword()
    {
        return view('rinvex.fort::frontend.password.forgot');
    }

    /**
     * Process the password reset request form.
     *
     * @param \Rinvex\Fort\Http\Requests\PasswordResetRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function processForgotPassword(PasswordResetRequest $request)
    {
        $result = app('rinvex.fort.resetter')->broker($this->getBroker())->sendResetLink($request->except(['_token']));

        switch ($result) {
            case ResetBrokerContract::LINK_SENT:
                return intend([
                    'intended' => route('home'),
                    'with'     => ['rinvex.fort.alert.success' => Lang::get($result)],
                ]);

            case ResetBrokerContract::INVALID_USER:
            default:
                return intend([
                    'back'       => true,
                    'withInput'  => $request->only('email'),
                    'withErrors' => ['email' => Lang::get($result)],
                ]);
        }
    }
}
