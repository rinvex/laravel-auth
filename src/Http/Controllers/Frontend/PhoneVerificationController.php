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
use Rinvex\Fort\Guards\SessionGuard;
use Rinvex\Fort\Http\Controllers\AbstractController;
use Rinvex\Fort\Http\Requests\Frontend\PhoneVerificationRequest;
use Rinvex\Fort\Http\Requests\Frontend\PhoneVerificationSendRequest;

class PhoneVerificationController extends AbstractController
{
    /**
     * Show the phone verification form.
     *
     * @param \Rinvex\Fort\Http\Requests\Frontend\PhoneVerificationSendRequest $request
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function request(PhoneVerificationSendRequest $request)
    {
        // If Two-Factor authentication failed, remember Two-Factor persistence
        Auth::guard($this->getGuard())->rememberTwoFactor();

        return view('rinvex/fort::frontend/verification.phone.request');
    }

    /**
     * Process the phone verification request form.
     *
     * @param \Rinvex\Fort\Http\Requests\Frontend\PhoneVerificationSendRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function send(PhoneVerificationSendRequest $request)
    {
        // Send phone verification notification
        $request->user($this->getGuard())->sendPhoneVerificationNotification(false, $request->get('method'));

        return intend([
            'route' => 'rinvex.fort.frontend.verification.phone.verify',
            'with'  => ['rinvex.fort.alert.success' => trans('rinvex/fort::frontend/messages.verification.phone.sent')],
        ]);
    }

    /**
     * Show the phone verification form.
     *
     * @param \Rinvex\Fort\Http\Requests\Frontend\PhoneVerificationRequest $request
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function verify(PhoneVerificationRequest $request)
    {
        // If Two-Factor authentication failed, remember Two-Factor persistence
        Auth::guard($this->getGuard())->rememberTwoFactor();

        $methods = session('rinvex.fort.twofactor.methods');

        return view('rinvex/fort::frontend/verification.phone.token', compact('methods'));
    }

    /**
     * Process the phone verification form.
     *
     * @param \Rinvex\Fort\Http\Requests\Frontend\PhoneVerificationRequest $request
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function process(PhoneVerificationRequest $request)
    {
        $guard  = $this->getGuard();
        $token  = $request->get('token');
        $user   = session('rinvex.fort.twofactor.user') ?: $request->user($this->getGuard());
        $result = Auth::guard($guard)->attemptTwoFactor($user, $token);

        switch ($result) {
            case SessionGuard::AUTH_PHONE_VERIFIED:
                return intend([
                    'route' => 'rinvex.fort.frontend.user.settings',
                    'with'  => ['rinvex.fort.alert.success' => trans($result)],
                ]);

            case SessionGuard::AUTH_LOGIN:
                Auth::guard($guard)->login($user, session('rinvex.fort.twofactor.remember'), session('rinvex.fort.twofactor.persistence'));

                return intend([
                    'intended' => url('/'),
                    'with'     => ['rinvex.fort.alert.success' => trans($result)],
                ]);

            case SessionGuard::AUTH_TWOFACTOR_FAILED:
            default:
                // If Two-Factor authentication failed, remember Two-Factor persistence
                Auth::guard($guard)->rememberTwoFactor();

                return intend([
                    'back'       => true,
                    'withInput'  => $request->only(['token']),
                    'withErrors' => ['token' => trans($result)],
                ]);
        }
    }
}
