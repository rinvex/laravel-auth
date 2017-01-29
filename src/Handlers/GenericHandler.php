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

namespace Rinvex\Fort\Handlers;

use Illuminate\Http\Request;
use Rinvex\Fort\Models\Role;
use Rinvex\Fort\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Auth\Authenticatable;
use Rinvex\Fort\Notifications\RegistrationSuccessNotification;
use Rinvex\Fort\Notifications\VerificationSuccessNotification;
use Rinvex\Fort\Notifications\AuthenticationLockoutNotification;

class GenericHandler
{
    /**
     * The container instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $app;

    /**
     * Create a new fort event listener instance.
     *
     * @param \Illuminate\Contracts\Container\Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $dispatcher
     */
    public function subscribe(Dispatcher $dispatcher)
    {
        $dispatcher->listen(Lockout::class, __CLASS__.'@authLockout');
        $dispatcher->listen('rinvex.fort.register.success', __CLASS__.'@registerSuccess');
        $dispatcher->listen('rinvex.fort.register.social.success', __CLASS__.'@registerSocialSuccess');
        $dispatcher->listen('rinvex.fort.emailverification.success', __CLASS__.'@emailVerificationSuccess');
    }

    /**
     * Listen to the authentication lockout event.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function authLockout(Request $request)
    {
        if (config('rinvex.fort.throttle.lockout_email')) {
            $user = get_login_field($loginfield = $request->get('loginfield')) == 'email' ? User::where('email', $loginfield)->first() : User::where('username', $loginfield)->first();

            $user->notify(new AuthenticationLockoutNotification($request));
        }
    }

    /**
     * Listen to the register success event.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return void
     */
    public function registerSuccess(Authenticatable $user)
    {
        // Send welcome email
        if (config('rinvex.fort.registration.welcome_email')) {
            $user->notify(new RegistrationSuccessNotification());
        }

        // Attach default role to the registered user
        if ($default = $this->app['config']->get('rinvex.fort.registration.default_role')) {
            if ($role = Role::where('slug', $default)->first()) {
                $user->roles()->attach($role);
            }
        }
    }

    /**
     * Listen to the register social success event.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return void
     */
    public function registerSocialSuccess(Authenticatable $user)
    {
        // Send welcome email
        if (config('rinvex.fort.registration.welcome_email')) {
            $user->notify(new RegistrationSuccessNotification(true));
        }

        // Attach default role to the registered user
        if ($default = $this->app['config']->get('rinvex.fort.registration.default_role')) {
            if ($role = Role::where('slug', $default)->first()) {
                $user->roles()->attach($role);
            }
        }
    }

    /**
     * Listen to the email verification success.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return void
     */
    public function emailVerificationSuccess(Authenticatable $user)
    {
        if (config('rinvex.fort.emailverification.success_notification')) {
            $user->notify(new VerificationSuccessNotification($user->active));
        }
    }
}
