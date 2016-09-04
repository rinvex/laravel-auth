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

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Rinvex\Fort\Guards\SessionGuard;
use Rinvex\Fort\Http\Requests\PhoneVerification;
use Rinvex\Fort\Http\Requests\PhoneVerificationRequest;

class VerifyPhoneController extends FoundationController
{
    /**
     * Show the phone verification request form.
     *
     * @param \Rinvex\Fort\Http\Requests\PhoneVerificationRequest $request
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function showPhoneVerificationRequest(PhoneVerificationRequest $request)
    {
        // If Two-Factor authentication failed, remember Two-Factor persistence
        Auth::guard($this->getGuard())->rememberTwoFactor();

        return view('rinvex.fort::verification.phone.request');
    }

    /**
     * Process the phone verification request form.
     *
     * @param \Rinvex\Fort\Http\Requests\PhoneVerificationRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function processPhoneVerificationRequest(PhoneVerificationRequest $request)
    {
        $status = app('rinvex.fort.verifier')
            ->broker($this->getBroker())
            ->sendPhoneVerification(Auth::guard($this->getGuard())->user(), $request->get('method')) ? 'sent' : 'failed';

        return intend([
            'intended' => route('rinvex.fort.verification.phone.verify'),
            'with'     => ['rinvex.fort.alert.success' => Lang::get('rinvex.fort::message.verification.phone.'.$status)],
        ]);
    }

    /**
     * Show the phone verification form.
     *
     * @param \Rinvex\Fort\Http\Requests\PhoneVerification $request
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function showPhoneVerification(PhoneVerification $request)
    {
        // If Two-Factor authentication failed, remember Two-Factor persistence
        Auth::guard($this->getGuard())->rememberTwoFactor();

        $methods = session('rinvex.fort.twofactor.methods');

        return view('rinvex.fort::verification.phone.token', compact('methods'));
    }

    /**
     * Process the phone verification form.
     *
     * @param \Rinvex\Fort\Http\Requests\PhoneVerification $request
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function processPhoneVerification(PhoneVerification $request)
    {
        $token  = $request->get('token');
        $result = Auth::guard($this->getGuard())->attemptTwoFactor(Auth::guard($this->getGuard())->user(), $token);

        switch ($result) {
            case SessionGuard::AUTH_PHONE_VERIFIED:
                return intend([
                    'intended' => route('rinvex.fort.account.page'),
                    'with'     => ['rinvex.fort.alert.success' => Lang::get($result)],
                ]);

            case SessionGuard::AUTH_LOGIN:
                return intend([
                    'intended' => route('home'),
                    'with'     => ['rinvex.fort.alert.success' => Lang::get($result)],
                ]);

            case SessionGuard::AUTH_TWOFACTOR_FAILED:
            default:
                // If Two-Factor authentication failed, remember Two-Factor persistence
                Auth::guard($this->getGuard())->rememberTwoFactor();

                return intend([
                    'back'       => true,
                    'withInput'  => $request->only(['token']),
                    'withErrors' => ['token' => Lang::get($result)],
                ]);
        }
    }
}
