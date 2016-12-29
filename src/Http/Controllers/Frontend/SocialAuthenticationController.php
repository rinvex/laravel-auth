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

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Rinvex\Fort\Contracts\UserRepositoryContract;

class SocialAuthenticationController extends AuthenticationController
{
    /**
     * Redirect to Github for authentication.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToGithub()
    {
        return Socialite::driver('github')->redirect();
    }

    /**
     * Handle Github authentication callback.
     *
     * @param \Illuminate\Http\Request                      $request
     * @param \Rinvex\Fort\Contracts\UserRepositoryContract $userRepository
     *
     * @return \Illuminate\Http\Response
     */
    public function handleGithubCallback(Request $request, UserRepositoryContract $userRepository)
    {
        try {
            $githubUser = Socialite::driver('github')->user();
        } catch (Exception $e) {
            return intend([
                'route' => 'rinvex.fort.frontend.auth.social.github',
            ]);
        }

        $user = app('rinvex.fort.user')->findWhereHas(['socialites', function ($query) use ($githubUser) {
            $query->where('provider', 'github');
            $query->where('provider_uid', $githubUser->id);
        }])->first();

        if (! $user) {
            // Prepare registration data
            $input = $request->except(['_method', '_token']) + [
                'email'    => $githubUser->email,
                'username' => $githubUser->username,
                'password' => str_random(),
                'active'   => ! config('rinvex.fort.registration.moderated'),
            ];

            // Fire the register start event
            $result = event('rinvex.fort.register.social.start', [$input]);

            // Create user
            $user = $userRepository->create($input);

            // Fire the register success event
            event('rinvex.fort.register.social.success', [$result]);

            $user->socialites()->create([
                'user_id'      => 'github',
                'provider'     => 'github',
                'provider_uid' => $githubUser->id,
            ]);
        }

        $result = Auth::guard($this->getGuard())->login($user, true);

        return $this->getLoginResponse($request, $result);
    }
}
