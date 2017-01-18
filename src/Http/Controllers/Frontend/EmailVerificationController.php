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
use Rinvex\Fort\Contracts\EmailVerificationBrokerContract;
use Rinvex\Fort\Http\Requests\Frontend\EmailVerificationRequest;

class EmailVerificationController extends AbstractController
{
    /**
     * Show the email verification request form.
     *
     * @return \Illuminate\Http\Response
     */
    public function request(EmailVerificationRequest $request)
    {
        return view('rinvex/fort::frontend/verification.email.request');
    }

    /**
     * Process the email verification request form.
     *
     * @param \Rinvex\Fort\Http\Requests\Frontend\EmailVerificationRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function send(EmailVerificationRequest $request)
    {
        $result = app('rinvex.fort.emailverification')
            ->broker($this->getBroker())
            ->send($request->only('email'));

        switch ($result) {
            case EmailVerificationBrokerContract::LINK_SENT:
                return intend([
                    'url'  => '/',
                    'with' => ['success' => trans($result)],
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
     * @param \Rinvex\Fort\Http\Requests\Frontend\EmailVerificationRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function verify(EmailVerificationRequest $request)
    {
        $result = app('rinvex.fort.emailverification')->broker($this->getBroker())->verify($request->only(['email', 'token']));

        switch ($result) {
            case EmailVerificationBrokerContract::EMAIL_VERIFIED:
                return intend([
                    'route' => $request->user() ? 'rinvex.fort.frontend.auth.login' : 'rinvex.fort.frontend.user.settings',
                    'with'  => ['success' => trans($result)],
                ]);

            case EmailVerificationBrokerContract::INVALID_USER:
            case EmailVerificationBrokerContract::INVALID_TOKEN:
            default:
                return intend([
                    'route'      => 'rinvex.fort.frontend.verification.email.request',
                    'withInput'  => $request->only('email'),
                    'withErrors' => ['token' => trans($result)],
                ]);
        }
    }
}
