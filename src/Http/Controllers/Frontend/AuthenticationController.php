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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Rinvex\Fort\Guards\SessionGuard;
use Rinvex\Fort\Http\Requests\UserAuthentication;
use Rinvex\Fort\Http\Controllers\AbstractController;

class AuthenticationController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    protected $middlewareWhitelist = ['logout'];

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
     * Show the login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLogin()
    {
        return view('rinvex.fort::frontend.authentication.login');
    }

    /**
     * Process to the login form.
     *
     * @param \Rinvex\Fort\Http\Requests\UserAuthentication $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function processLogin(UserAuthentication $request)
    {
        // Prepare variables
        $remember    = $request->has('remember');
        $loginField  = get_login_field($request->get('loginfield'));
        $credentials = [
            $loginField => $request->input('loginfield'),
            'password'  => $request->input('password'),
        ];

        $result = Auth::guard($this->getGuard())->attempt($credentials, $remember);

        return $this->getLoginResponse($request, $result);
    }

    /**
     * Logout currently logged in user.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        $result = Auth::guard($this->getGuard())->logout();

        return intend([
            'intended' => route('home'),
            'with'     => ['rinvex.fort.alert.warning' => Lang::get($result)],
        ]);
    }

    /**
     * Get login response upon the given request & result.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $result
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function getLoginResponse(Request $request, $result)
    {
        switch ($result) {
            // Too many failed logins, user locked out
            case SessionGuard::AUTH_LOCKED_OUT:
                $seconds = Auth::guard($this->getGuard())->secondsRemainingOnLockout($request);

                return intend([
                    'intended'   => route('home'),
                    'withInput'  => $request->only('loginfield', 'remember'),
                    'withErrors' => ['loginfield' => Lang::get($result, ['seconds' => $seconds])],
                ]);

            // Valid credentials, but user is unverified; Can NOT login!
            case SessionGuard::AUTH_UNVERIFIED:
                return intend([
                    'intended'   => route('rinvex.fort.verification.email'),
                    'withErrors' => ['email' => Lang::get($result)],
                ]);

            // Valid credentials, but user is moderated; Can NOT login!
            case SessionGuard::AUTH_MODERATED:
                return intend([
                    'home'       => true,
                    'withErrors' => ['rinvex.fort.auth.moderated' => Lang::get($result)],
                ]);

            // Wrong credentials, failed login
            case SessionGuard::AUTH_FAILED:
                return intend([
                    'back'       => true,
                    'withInput'  => $request->only('loginfield', 'remember'),
                    'withErrors' => ['loginfield' => Lang::get($result)],
                ]);

            // Two-Factor authentication required
            case SessionGuard::AUTH_TWOFACTOR_REQUIRED:
                $route = ! isset(session('rinvex.fort.twofactor.methods')['totp']) ? 'rinvex.fort.verification.phone' : 'rinvex.fort.verification.phone.verify';

                return intend([
                    'route' => $route,
                    'with'  => ['rinvex.fort.alert.warning' => Lang::get($result)],
                ]);

            // Login successful and everything is fine!
            case SessionGuard::AUTH_LOGIN:
            default:
                return intend([
                    'intended' => route('home'),
                    'with'     => ['rinvex.fort.alert.success' => Lang::get($result)],
                ]);
        }
    }
}
