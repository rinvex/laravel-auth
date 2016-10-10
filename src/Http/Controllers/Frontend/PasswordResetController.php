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

use Rinvex\Fort\Contracts\ResetBrokerContract;
use Rinvex\Fort\Http\Controllers\AbstractController;
use Rinvex\Fort\Http\Requests\Frontend\PasswordResetRequest;
use Rinvex\Fort\Http\Requests\Frontend\PasswordResetSendRequest;

class PasswordResetController extends AbstractController
{
    /**
     * Create a new password reset controller instance.
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
    public function request()
    {
        return view('rinvex/fort::frontend.passwordreset.request');
    }

    /**
     * Process the password reset request form.
     *
     * @param \Rinvex\Fort\Http\Requests\Frontend\PasswordResetSendRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function send(PasswordResetSendRequest $request)
    {
        $result = app('rinvex.fort.resetter')->broker($this->getBroker())->sendResetLink($request->except(['_token']));

        switch ($result) {
            case ResetBrokerContract::LINK_SENT:
                return intend([
                    'intended' => url('/'),
                    'with'     => ['rinvex.fort.alert.success' => trans($result)],
                ]);

            case ResetBrokerContract::INVALID_USER:
            default:
                return intend([
                    'back'       => true,
                    'withInput'  => $request->only('email'),
                    'withErrors' => ['email' => trans($result)],
                ]);
        }
    }

    /**
     * Show the password reset form.
     *
     * @param \Rinvex\Fort\Http\Requests\Frontend\PasswordResetRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function reset(PasswordResetRequest $request)
    {
        $email  = $request->get('email');
        $token  = $request->get('token');
        $result = app('rinvex.fort.resetter')->broker($this->getBroker())->validateReset($request->except(['_token']));

        switch ($result) {
            case ResetBrokerContract::INVALID_USER:
            case ResetBrokerContract::INVALID_TOKEN:
                return intend([
                    'route'      => 'rinvex.fort.frontend.passwordreset.request',
                    'withInput'  => $request->only('email'),
                    'withErrors' => ['email' => trans($result)],
                ]);

            case ResetBrokerContract::VALID_TOKEN:
            default:
                return view('rinvex/fort::frontend.passwordreset.reset')->with(compact('token', 'email'));
        }
    }

    /**
     * Process the password reset form.
     *
     * @param \Rinvex\Fort\Http\Requests\Frontend\PasswordResetRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function process(PasswordResetRequest $request)
    {
        $result = app('rinvex.fort.resetter')->broker($this->getBroker())->reset($request->except(['_token']));

        switch ($result) {
            case ResetBrokerContract::RESET_SUCCESS:
                return intend([
                    'intended' => url('/'),
                    'with'     => ['rinvex.fort.alert.success' => trans($result)],
                ]);

            case ResetBrokerContract::INVALID_USER:
            case ResetBrokerContract::INVALID_TOKEN:
            case ResetBrokerContract::INVALID_PASSWORD:
            default:
                return intend([
                    'back'       => true,
                    'withInput'  => $request->only('email'),
                    'withErrors' => ['email' => trans($result)],
                ]);
        }
    }
}
