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

use Rinvex\Fort\Http\Controllers\AbstractController;
use Rinvex\Fort\Contracts\VerificationBrokerContract;
use Rinvex\Fort\Http\Requests\Frontend\EmailVerification;

class EmailVerificationController extends AbstractController
{
    /**
     * Show the email verification request form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showEmailVerificationRequest()
    {
        return view('rinvex.fort::frontend.verification.email.request');
    }

    /**
     * Process the email verification request form.
     *
     * @param \Rinvex\Fort\Http\Requests\Frontend\EmailVerification $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function processEmailVerificationRequest(EmailVerification $request)
    {
        $result = app('rinvex.fort.verifier')
            ->broker($this->getBroker())
            ->sendVerificationLink($request->except('_token'));

        switch ($result) {
            case VerificationBrokerContract::LINK_SENT:
                return intend([
                    'intended' => url('/'),
                    'with'     => ['rinvex.fort.alert.success' => trans($result)],
                ]);

            default:
                return intend([
                    'back'       => true,
                    'withInput'  => $request->only('email'),
                    'withErrors' => ['email' => trans($result)],
                ]);
        }
    }

    /**
     * Process the email verification.
     *
     * @param \Rinvex\Fort\Http\Requests\Frontend\EmailVerification $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function processEmailVerification(EmailVerification $request)
    {
        $result = app('rinvex.fort.verifier')->broker($this->getBroker())->verify($request->except('_token'));

        switch ($result) {
            case VerificationBrokerContract::EMAIL_VERIFIED:
                return intend([
                    'intended' => url('/'),
                    'with'     => ['rinvex.fort.alert.success' => trans($result)],
                ]);

            case VerificationBrokerContract::INVALID_USER:
                return intend([
                    'intended'   => route('rinvex.fort.frontend.verification.email'),
                    'withInput'  => $request->only('email'),
                    'withErrors' => ['email' => trans($result)],
                ]);

            case VerificationBrokerContract::INVALID_TOKEN:
            default:
                return intend([
                    'intended'   => route('rinvex.fort.frontend.verification.email'),
                    'withInput'  => $request->only('email'),
                    'withErrors' => ['token' => trans($result)],
                ]);
        }
    }
}
