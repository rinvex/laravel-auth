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

use Rinvex\Fort\Models\User;
use Rinvex\Fort\Http\Controllers\AbstractController;
use Rinvex\Fort\Http\Requests\Frontend\UserRegistrationRequest;

class RegistrationController extends AbstractController
{
    /**
     * Create a new registration controller instance.
     */
    public function __construct()
    {
        $this->middleware($this->getGuestMiddleware(), ['except' => $this->middlewareWhitelist]);
    }

    /**
     * Show the registration form.
     *
     * @param \Rinvex\Fort\Http\Requests\Frontend\UserRegistrationRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function form(UserRegistrationRequest $request)
    {
        return view('rinvex/fort::frontend/authentication.register');
    }

    /**
     * Process the registration form.
     *
     * @param \Rinvex\Fort\Http\Requests\Frontend\UserRegistrationRequest $request
     * @param \Rinvex\Fort\Models\User                                    $user
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function register(UserRegistrationRequest $request, User $user)
    {
        // Prepare registration data
        $input = $request->only(['username', 'email', 'password', 'password_confirmation']);
        $active = ['active' => ! config('rinvex.fort.registration.moderated')];

        // Fire the register start event
        event('rinvex.fort.register.start', [$input + $active]);

        $result = $user->create($input + $active);

        // Fire the register success event
        event('rinvex.fort.register.success', [$result]);

        // Send verification if required
        if (config('rinvex.fort.emailverification.required')) {
            app('rinvex.fort.emailverification')->broker()->send(['email' => $input['email']]);

            // Registration completed, verification required
            return intend([
                'intended' => url('/'),
                'with'     => ['success' => trans('rinvex/fort::messages.register.success_verify')],
            ]);
        }

        // Registration completed successfully
        return intend([
            'route' => 'rinvex.fort.frontend.auth.login',
            'with'  => ['success' => trans('rinvex/fort::messages.register.success')],
        ]);
    }
}
