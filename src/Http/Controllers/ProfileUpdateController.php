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

use Rinvex\Country\Models\Country;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Rinvex\Fort\Http\Requests\ProfileUpdate;
use Rinvex\Fort\Contracts\UserRepositoryContract;

class ProfileUpdateController extends AbstractController
{
    /**
     * The users repository instance.
     *
     * @var \Rinvex\Fort\Contracts\UserRepositoryContract
     */
    protected $users;

    /**
     * Create a new account controller instance.
     *
     * @param \Rinvex\Fort\Contracts\UserRepositoryContract $users
     *
     * @return void
     */
    public function __construct(UserRepositoryContract $users)
    {
        $this->users = $users;

        $this->middleware($this->getAuthMiddleware(), ['except' => $this->middlewareWhitelist]);
    }

    /**
     * Show the account update form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showProfileUpdate(Country $country)
    {
        $twoFactor = $this->currentUser()->getTwoFactor();
        $countries = $country->findAll()->pluck('name.common', 'iso_3166_1_alpha2');

        return view('rinvex.fort::profile.page', compact('twoFactor', 'countries'));
    }

    /**
     * Process the account update form.
     *
     * @param \Rinvex\Fort\Http\Requests\ProfileUpdate $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function processProfileUpdate(ProfileUpdate $request)
    {
        $currentUser = $this->currentUser();
        $data        = $request->except(['_token', 'id']);
        $twoFactor   = $currentUser->getTwoFactor();

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $emailVerification = $data['email'] != $currentUser->email ? [
            'email_verified'    => false,
            'email_verified_at' => null,
        ] : [];

        $phoneVerification = $data['phone'] != $currentUser->phone ? [
            'phone_verified'    => false,
            'phone_verified_at' => null,
        ] : [];

        $countryVerification = $data['country'] !== $currentUser->country;

        if ($phoneVerification || $countryVerification) {
            array_set($twoFactor, 'phone.enabled', false);
        }

        $this->users->update($request->get('id'), $data + $emailVerification + $phoneVerification + $twoFactor);

        return intend([
            'back' => true,
            'with' => [
                          'rinvex.fort.alert.success' => Lang::get('rinvex.fort::message.account.'.(! empty($emailVerification) ? 'reverify' : 'updated')),
                      ] + ($twoFactor !== $currentUser->getTwoFactor() ? ['rinvex.fort.alert.warning' => Lang::get('rinvex.fort::message.verification.twofactor.phone.auto_disabled')] : []),
        ]);
    }

    /**
     * Get current user.
     *
     * @return \Rinvex\Fort\Contracts\AuthenticatableContract
     */
    protected function currentUser()
    {
        return Auth::guard($this->getGuard())->user();
    }
}
