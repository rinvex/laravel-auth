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

use Rinvex\Country\Loader;
use Illuminate\Support\Facades\Auth;
use Rinvex\Fort\Contracts\UserRepositoryContract;
use Rinvex\Fort\Http\Requests\Frontend\ProfileUpdate;
use Rinvex\Fort\Http\Controllers\AuthorizedController;

class ProfileUpdateController extends AuthorizedController
{
    /**
     * The user repository instance.
     *
     * @var \Rinvex\Fort\Contracts\UserRepositoryContract
     */
    protected $userRepository;

    /**
     * Create a new profile update controller instance.
     *
     * @param \Rinvex\Fort\Contracts\UserRepositoryContract $userRepository
     *
     * @return void
     */
    public function __construct(UserRepositoryContract $userRepository)
    {
        parent::__construct();

        $this->userRepository = $userRepository;
    }

    /**
     * Show the account update form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showProfileUpdate()
    {
        $countries = Loader::countries();
        $twoFactor = $this->currentUser()->getTwoFactor();

        return view('rinvex.fort::frontend.profile.page', compact('twoFactor', 'countries'));
    }

    /**
     * Process the account update form.
     *
     * @param \Rinvex\Fort\Http\Requests\Frontend\ProfileUpdate $request
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

        $this->userRepository->update($request->get('id'), $data + $emailVerification + $phoneVerification + $twoFactor);

        return intend([
            'back' => true,
            'with' => [
                          'rinvex.fort.alert.success' => trans('rinvex.fort::frontend/messages.account.'.(! empty($emailVerification) ? 'reverify' : 'updated')),
                      ] + ($twoFactor !== $currentUser->getTwoFactor() ? ['rinvex.fort.alert.warning' => trans('rinvex.fort::frontend/messages.verification.twofactor.phone.auto_disabled')] : []),
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
