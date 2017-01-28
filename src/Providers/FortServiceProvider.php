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
use Illuminate\Support\Facades\Auth;
use Rinvex\Fort\Guards\SessionGuard;
use Illuminate\Support\ServiceProvider;
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

        // Override Exception Handler
        $this->overrideExceptionHandler();

        // Register the deferred Fort Service Provider
        $this->app->register(FortDeferredServiceProvider::class);
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Router $router)
    {
        // Override middlware
        $this->overrideMiddleware($router);

        // Load routes
        $this->loadRoutes($router);

        // Load views
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'rinvex/fort');

        // Load language phrases
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', 'rinvex/fort');

        if ($this->app->runningInConsole()) {
            // Load migrations
            $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

            // Publish Resources
            $this->publishResources();
        }

        // Subscribe the registered event listener
        $this->app['events']->subscribe('rinvex.fort.listener');

        // Override session guard
        $this->overrideSessionGuard();

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

    protected function overrideMiddleware(Router $router)
    {
        // Override "web" middleware group on the fly
        $router->middlewareGroup('web', [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Rinvex\Fort\Http\Middleware\Abilities::class,
        ]);

        // Override route middleware on the fly
        $router->aliasMiddleware('auth', Authenticate::class);
        $router->aliasMiddleware('guest', RedirectIfAuthenticated::class);
    }

    protected function overrideExceptionHandler()
    {
        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \Rinvex\Fort\Exceptions\ExceptionHandler::class
        );
    }
}
