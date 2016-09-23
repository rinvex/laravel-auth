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

namespace Rinvex\Fort\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Rinvex\Support\Http\Requests\FormRequest;

class PhoneVerification extends FormRequest
{
    /**
     * {@inheritdoc}
     */
    public function forbiddenResponse()
    {
        $user        = Auth::guard()->user();
        $attemptUser = Auth::guard()->attemptUser();

        return $user && ! $user->country ? intend([
            // Logged in user, no country, phone verification attempt (account update)
            'intended'   => route('rinvex.fort.frontend.account.page'),
            'withErrors' => ['country' => trans('rinvex.fort::frontend/messages.account.country_required')],
        ]) : ($attemptUser && ! $attemptUser->country ? intend([
            // Login attempt, no country, enabled Two-Factor
            'intended'   => route('rinvex.fort.frontend.auth.login'),
            'withErrors' => ['rinvex.fort.auth.country' => trans('rinvex.fort::frontend/messages.verification.twofactor.phone.country_required')],
        ]) : intend([
            // No login attempt, no user instance, phone verification attempt
            'intended'   => route('rinvex.fort.frontend.auth.login'),
            'withErrors' => ['rinvex.fort.auth.required' => trans('rinvex.fort::frontend/messages.auth.session.required')],
        ]));
    }

    /**
     * {@inheritdoc}
     */
    public function response(array $errors)
    {
        // If we've got errors, remember Two-Factor persistence
        Auth::guard()->rememberTwoFactor();

        return parent::response($errors);
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user      = Auth::guard()->user() ?: Auth::guard()->attemptUser();
        $providers = config('rinvex.fort.twofactor.providers');

        return ! $user || ! $user->country || ! in_array('phone', $providers) ? false : true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->isMethod('post') ? [
            'token' => 'required|numeric',
        ] : [];
    }
}
