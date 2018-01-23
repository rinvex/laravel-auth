<?php

declare(strict_types=1);

namespace Rinvex\Fort\Handlers;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Auth\Authenticatable;
use Rinvex\Fort\Notifications\RegistrationSuccessNotification;
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
        $dispatcher->listen(Login::class, __CLASS__.'@authLogin');
        $dispatcher->listen(Lockout::class, __CLASS__.'@authLockout');
        $dispatcher->listen('rinvex.fort.register.success', __CLASS__.'@registerSuccess');
        $dispatcher->listen('rinvex.fort.register.social.success', __CLASS__.'@registerSocialSuccess');
    }

    /**
     * Listen to the authentication lockout event.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function authLockout(Request $request): void
    {
        if (config('rinvex.fort.throttle.lockout_email')) {
            $user = get_login_field($loginfield = $request->get('loginfield')) === 'email' ? app('rinvex.fort.user')->where('email', $loginfield)->first() : app('rinvex.fort.user')->where('username', $loginfield)->first();

            $user->notify(new AuthenticationLockoutNotification($request));
        }
    }

    /**
     * Listen to the authentication login event.
     *
     * @param \Illuminate\Auth\Events\Login $event
     *
     * @return void
     */
    public function authLogin(Login $event): void
    {
        ! config('rinvex.fort.persistence') === 'single' || $event->user->sessions()->delete();
    }

    /**
     * Listen to the register success event.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return void
     */
    public function registerSuccess(Authenticatable $user): void
    {
        // Send welcome email
        if (config('rinvex.fort.registration.welcome_email')) {
            $user->notify(new RegistrationSuccessNotification());
        }
    }

    /**
     * Listen to the register social success event.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return void
     */
    public function registerSocialSuccess(Authenticatable $user): void
    {
        // Send welcome email
        if (config('rinvex.fort.registration.welcome_email')) {
            $user->notify(new RegistrationSuccessNotification(true));
        }
    }
}
