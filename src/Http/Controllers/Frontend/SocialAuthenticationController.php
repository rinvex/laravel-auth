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
     * @return \Illuminate\Http\Response
     */
    public function handleGithubCallback(Request $request)
    {
        try {
            $githubUser = Socialite::driver('github')->user();
        } catch (Exception $e) {
            return intend([
                'intended' => route('rinvex.fort.auth.social.github'),
            ]);
        }

        $user = app('rinvex.fort.user')->whereHas('socialites', function ($query) use ($githubUser) {
            $query->where('provider', 'github')->where('provider_uid', $githubUser->id);
        })->first();

        if (! $user) {
            $user = Auth::guard($this->getGuard())->registerSocialite([
                'email'    => $githubUser->email,
                'username' => $githubUser->username,
            ]);

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
