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

use Illuminate\Support\Facades\Lang;
use Rinvex\Fort\Http\Requests\PasswordReset;
use Rinvex\Fort\Contracts\ResetBrokerContract;
use Rinvex\Fort\Http\Requests\PasswordResetRequest;

class ResetterController extends FoundationController
{
    /**
     * Whitelisted methods.
     *
     * Array of whitelisted methods which do not need to be authorized.
     *
     * @var array
     */
    protected $authWhitelist = [];

    /**
     * Create a new reset password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware($this->getGuestMiddleware(), ['except' => $this->authWhitelist]);
    }

    /**
     * Show the password reset request form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showPasswordResetRequest()
    {
        return view('rinvex.fort::password.request');
    }

    /**
     * Process the password reset request form.
     *
     * @param \Rinvex\Fort\Http\Requests\PasswordResetRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function processPasswordResetRequest(PasswordResetRequest $request)
    {
        $result = app('rinvex.fort.resetter')->broker($this->getBroker())->sendResetLink($request->except(['_token']));

        switch ($result) {
            case ResetBrokerContract::REQUEST_SENT:
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

    /**
     * Show the password reset form.
     *
     * @param \Rinvex\Fort\Http\Requests\PasswordReset $request
     *
     * @return \Illuminate\Http\Response
     */
    public function showPasswordReset(PasswordReset $request)
    {
        $email  = $request->get('email');
        $token  = $request->get('token');
        $result = app('rinvex.fort.resetter')->broker($this->getBroker())->validateReset($request->except(['_token']));

        switch ($result) {
            case ResetBrokerContract::INVALID_USER:
            case ResetBrokerContract::INVALID_TOKEN:
                return intend([
                    'intended'   => route('rinvex.fort.password.request'),
                    'withInput'  => $request->only('email'),
                    'withErrors' => ['email' => Lang::get($result)],
                ]);

            case ResetBrokerContract::VALID_TOKEN:
            default:
                return view('rinvex.fort::password.reset')->with(compact('token', 'email'));
        }
    }

    /**
     * Process the password reset form.
     *
     * @param \Rinvex\Fort\Http\Requests\PasswordReset $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function processPasswordReset(PasswordReset $request)
    {
        $result = app('rinvex.fort.resetter')->broker($this->getBroker())->reset($request->except(['_token']));

        switch ($result) {
            case ResetBrokerContract::RESET_SUCCESS:
                return intend([
                    'intended' => route('home'),
                    'with'     => ['rinvex.fort.alert.success' => Lang::get($result)],
                ]);

            case ResetBrokerContract::INVALID_USER:
            case ResetBrokerContract::INVALID_TOKEN:
            case ResetBrokerContract::INVALID_PASSWORD:
            default:
                return intend([
                    'intended'   => route('rinvex.fort.password.request'),
                    'withInput'  => $request->only('email'),
                    'withErrors' => ['email' => Lang::get($result)],
                ]);
        }
    }
}
