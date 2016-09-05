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
use Rinvex\Fort\Guards\SessionGuard;
use Rinvex\Fort\Http\Requests\UserRegistration;
use Rinvex\Fort\Http\Controllers\AbstractController;
use Rinvex\Fort\Contracts\VerificationBrokerContract;

class RegistrationController extends AbstractController
{
    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware($this->getGuestMiddleware(), ['except' => $this->middlewareWhitelist]);
    }

    /**
     * Show the registration form.
     *
     * @param \Rinvex\Fort\Http\Requests\UserRegistration $request
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegisteration(UserRegistration $request)
    {
        return view('rinvex.fort::frontend.authentication.register');
    }

    /**
     * Process the registration form.
     *
     * @param \Rinvex\Fort\Http\Requests\UserRegistration $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function processRegisteration(UserRegistration $request)
    {
        $result = Auth::guard($this->getGuard())->register($request->except('_token'));

        switch ($result) {

            // Registration completed, verification required
            case VerificationBrokerContract::LINK_SENT:
                return intend([
                    'home' => true,
                    'with' => ['rinvex.fort.alert.success' => Lang::get('rinvex.fort::message.register.success_verify')],
                ]);

            // Registration completed successfully
            case SessionGuard::AUTH_REGISTERED:
            default:
                return intend([
                    'intended' => route('rinvex.fort.frontend.auth.login'),
                    'with'     => ['rinvex.fort.alert.success' => Lang::get($result)],
                ]);

        }
    }
}
