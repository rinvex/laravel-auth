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
use Rinvex\Fort\Http\Controllers\AuthenticatedController;
use Rinvex\Fort\Http\Requests\Frontend\UserSettingsUpdateRequest;

class UserSettingsController extends AuthenticatedController
{
    /**
     * Show the account update form.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $countries = array_map(function ($country) {
            return $country['name'];
        }, countries());
        $twoFactor = $request->user($this->getGuard())->getTwoFactor();

        return view('rinvex/fort::frontend/user.settings', compact('twoFactor', 'countries'));
    }

    /**
     * Process the account update form.
     *
     * @param \Rinvex\Fort\Http\Requests\Frontend\UserSettingsUpdateRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(UserSettingsUpdateRequest $request)
    {
        $currentUser = $request->user($this->getGuard());
        $data = $request->only(array_intersect(array_keys($request->all()), $currentUser->getFillable()));
        $twoFactor = $currentUser->getTwoFactor();

        $emailVerification = array_get($data, 'email') != $currentUser->email ? [
            'email_verified'    => false,
            'email_verified_at' => null,
        ] : [];

        $phoneVerification = array_get($data, 'phone') != $currentUser->phone ? [
            'phone_verified'    => false,
            'phone_verified_at' => null,
        ] : [];

        $countryVerification = array_get($data, 'country') !== $currentUser->country;

        if ($twoFactor && ($phoneVerification || $countryVerification)) {
            array_set($twoFactor, 'two_factor.phone.enabled', false);
        }

        $currentUser->update($data + $emailVerification + $phoneVerification + $twoFactor);

        return intend([
            'back' => true,
            'with' => [
                          'success' => trans('rinvex/fort::messages.account.'.(! empty($emailVerification) ? 'reverify' : 'updated')),
                      ] + ($twoFactor !== $currentUser->getTwoFactor() ? ['warning' => trans('rinvex/fort::messages.verification.twofactor.phone.auto_disabled')] : []),
        ]);
    }
}
