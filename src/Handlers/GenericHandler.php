<?php

declare(strict_types=1);

namespace Rinvex\Fort\Handlers;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Registered;
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
        $dispatcher->listen(Login::class, __CLASS__.'@login');
        $dispatcher->listen(Lockout::class, __CLASS__.'@lockout');
        $dispatcher->listen(Registered::class, __CLASS__.'@registered');
    }

    /**
     * Listen to the authentication lockout event.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function lockout(Request $request): void
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
    public function login(Login $event): void
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
    public function registered(Authenticatable $user): void
    {
        // Send welcome email
        if (config('rinvex.fort.registration.welcome_email')) {
            $user->notify(new RegistrationSuccessNotification());
        }
    }
}
