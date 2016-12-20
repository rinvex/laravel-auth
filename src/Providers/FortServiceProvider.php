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

namespace Rinvex\Fort\Providers;

use Illuminate\Routing\Router;
use Rinvex\Fort\Guards\TokenGuard;
use Illuminate\Support\Facades\Auth;
use Rinvex\Fort\Guards\SessionGuard;
use Illuminate\Support\ServiceProvider;
use Rinvex\Fort\Http\Middleware\Abilities;
use Rinvex\Fort\Listeners\FortEventListener;
use Rinvex\Fort\Http\Middleware\Authenticate;
use Rinvex\Fort\Http\Middleware\RedirectIfAuthenticated;

class FortServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(realpath(__DIR__.'/../../config/config.php'), 'rinvex.fort');

        // Register the event listener
        $this->app->bind('rinvex.fort.listener', FortEventListener::class);

        // Register the deferred Fort Service Provider
        $this->app->register(FortDeferredServiceProvider::class);
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Router $router)
    {
        // Publish Resources
        $this->publishResources();

        // Add middleware group on the fly
        $router->middlewareGroup('abilities', [Abilities::class]);

        // Override route middleware on the fly
        $router->middleware('auth', Authenticate::class);
        $router->middleware('guest', RedirectIfAuthenticated::class);

        // Load routes
        $this->loadRoutes($router);

        // Load views
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'rinvex/fort');

        // Load language phrases
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', 'rinvex/fort');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        // Subscribe the registered event listener
        $this->app['events']->subscribe('rinvex.fort.listener');

        // Override user provider
        $this->overrideUserProvider();

        // Override session guard
        $this->overrideSessionGuard();

        // Override token guard
        $this->overrideTokenGuard();

        // Share current user instance with all views
        $this->app['view']->composer('*', function ($view) {
            $view->with('currentUser', Auth::user());
        });
    }

    /**
     * Publish resources.
     *
     * @return void
     */
    protected function publishResources()
    {
        // Publish config
        $this->publishes([
            realpath(__DIR__.'/../../config/config.php') => config_path('rinvex.fort.php'),
        ], 'config');

        // Publish migrations
        $this->publishes([
            realpath(__DIR__.'/../../database/migrations') => database_path('migrations'),
        ], 'migrations');

        // Publish language phrases
        $this->publishes([
            realpath(__DIR__.'/../../resources/lang') => resource_path('lang/vendor/rinvex/fort'),
        ], 'lang');

        // Publish views
        $this->publishes([
            realpath(__DIR__.'/../../resources/views') => resource_path('views/vendor/rinvex/fort'),
        ], 'views');
    }

    /**
     * Load the routes.
     *
     * @param \Illuminate\Routing\Router $router
     *
     * @return void
     */
    public function loadRoutes(Router $router)
    {
        // Load routes
        if ($this->app->routesAreCached()) {
            $this->app->booted(function () {
                require $this->app->getCachedRoutesPath();
            });
        } else {
            // Load the application routes
            require __DIR__.'/../../routes/web.backend.php';
            require __DIR__.'/../../routes/web.frontend.php';

            $this->app->booted(function () use ($router) {
                $router->getRoutes()->refreshNameLookups();
            });
        }
    }

    /**
     * Add custom user provider.
     *
     * @return void
     */
    protected function overrideUserProvider()
    {
        $this->app['auth']->provider('eloquent', function ($app, array $config) {
            // Return an instance of Rinvex\Fort\Contracts\UserRepositoryContract
            return $this->app['rinvex.fort.user'];
        });
    }

    /**
     * Override session guard.
     *
     * @return void
     */
    protected function overrideSessionGuard()
    {
        // Add custom session guard
        $this->app['auth']->extend('session', function ($app, $name, array $config) {
            $provider = $app['auth']->createUserProvider($config['provider']);

            $guard = new SessionGuard($name, $provider, $app['session.store'], $app['request']);

            // When using the remember me functionality of the authentication services we
            // will need to be set the encryption instance of the guard, which allows
            // secure, encrypted cookie values to get generated for those cookies.
            if (method_exists($guard, 'setCookieJar')) {
                $guard->setCookieJar($this->app['cookie']);
            }

            if (method_exists($guard, 'setDispatcher')) {
                $guard->setDispatcher($this->app['events']);
            }

            if (method_exists($guard, 'setRequest')) {
                $guard->setRequest($this->app->refresh('request', $guard, 'setRequest'));
            }

            return $guard;
        });
    }

    /**
     * Override Token guard.
     *
     * @return void
     */
    protected function overrideTokenGuard()
    {
        // Add custom session guard
        $this->app['auth']->extend('token', function ($app, $name, array $config) {
            // The token guard implements a basic API token based guard implementation
            // that takes an API token field from the request and matches it to the
            // user in the database or another persistence layer where users are.
            $guard = new TokenGuard(
                $app['auth']->createUserProvider($config['provider']),
                $app['request']
            );

            $app->refresh('request', $guard, 'setRequest');

            return $guard;
        });
    }
}
