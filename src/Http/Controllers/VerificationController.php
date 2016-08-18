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
use Rinvex\Fort\Http\Requests\EmailVerification;
use Rinvex\Fort\Http\Requests\PhoneVerification;
use Rinvex\Fort\Contracts\VerificationBrokerContract;
use Rinvex\Fort\Http\Requests\PhoneVerificationRequest;

class VerificationController extends FoundationController
{
    /**
     * Show the email verification request form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showEmailVerificationRequest()
    {
        return view('rinvex.fort::verification.email.request');
    }

    /**
     * Process the email verification request form.
     *
     * @param \Rinvex\Fort\Http\Requests\EmailVerification $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function processEmailVerificationRequest(EmailVerification $request)
    {
        $result = app('rinvex.fort.verifier')
            ->broker($this->getBroker())
            ->sendVerificationLink($request->except('_token'));

        switch ($result) {
            case VerificationBrokerContract::REQUEST_SENT:
                return intend([
                    'intended' => route('home'),
                    'with'     => ['rinvex.fort.alert.success' => Lang::get($result)],
                ]);

            default:
                return intend([
                    'back'       => true,
                    'withInput'  => $request->only('email'),
                    'withErrors' => ['email' => Lang::get($result)],
                ]);
        }
    }

    /**
     * Process the email verification.
     *
     * @param \Rinvex\Fort\Http\Requests\EmailVerification $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function processEmailVerification(EmailVerification $request)
    {
        $result = app('rinvex.fort.verifier')->broker($this->getBroker())->verify($request->except('_token'));

        switch ($result) {
            case VerificationBrokerContract::EMAIL_VERIFIED:
                return intend([
                    'intended' => route('home'),
                    'with'     => ['rinvex.fort.alert.success' => Lang::get($result)],
                ]);

            case VerificationBrokerContract::INVALID_USER;
                return intend([
                    'intended'   => route('rinvex.fort.verification.email'),
                    'withInput'  => $request->only('email'),
                    'withErrors' => ['email' => Lang::get($result)],
                ]);

            case VerificationBrokerContract::INVALID_TOKEN;
            default:
                return intend([
                    'intended'   => route('rinvex.fort.verification.email'),
                    'withInput'  => $request->only('email'),
                    'withErrors' => ['token' => Lang::get($result)],
                ]);
        }
    }

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
            ->sendPhoneVerification($this->currentUser, $request->get('method')) ? 'sent' : 'failed';

        return intend([
            'intended' => route('rinvex.fort.verification.phone.token'),
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
        $result = Auth::guard($this->getGuard())->attemptTwoFactor($this->currentUser, $token);

        switch ($result) {
            case SessionGuard::AUTH_PHONE_VERIFIED:
                return intend([
                    'intended' => route('rinvex.fort.account.page'),
                    'with'     => ['rinvex.fort.alert.success' => Lang::get($result)],
                ]);

            case SessionGuard::AUTH_LOGIN;
                return intend([
                    'intended' => route('home'),
                    'with'     => ['rinvex.fort.alert.success' => Lang::get($result)],
                ]);

            case SessionGuard::AUTH_TWOFACTOR_FAILED;
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
