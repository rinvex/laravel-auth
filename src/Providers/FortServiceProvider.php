<?php

declare(strict_types=1);

namespace Rinvex\Fort\Providers;

use Illuminate\Routing\Router;
use Rinvex\Fort\Guards\SessionGuard;
use Rinvex\Fort\Services\AccessGate;
use Illuminate\Support\ServiceProvider;
use Rinvex\Fort\Handlers\GenericHandler;
use Rinvex\Fort\Http\Middleware\Abilities;
use Rinvex\Fort\Http\Middleware\NoHttpCache;
use Rinvex\Fort\Http\Middleware\Authenticate;
use Illuminate\Console\DetectsApplicationNamespace;
use Rinvex\Fort\Http\Middleware\UpdateLastActivity;
use Rinvex\Fort\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;

class FortServiceProvider extends ServiceProvider
{
    use DetectsApplicationNamespace;

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(realpath(__DIR__.'/../../config/config.php'), 'rinvex.fort');

        if (config('rinvex.fort.boot.override_exceptionhandler')) {
            // Override Exception Handler
            $this->overrideExceptionHandler();
        }

        // Register Access Gate Binding
        $this->registerAccessGate();
    }

    /**
     * Register the access gate service.
     *
     * @return void
     */
    protected function registerAccessGate()
    {
        $this->app->singleton(GateContract::class, function ($app) {
            return new AccessGate($app, function () use ($app) {
                return call_user_func($app['auth']->userResolver());
            });
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Router $router)
    {
        if (config('rinvex.fort.boot.override_middleware')) {
            // Override middlware
            $this->overrideMiddleware($router);
        }

        // Load routes
        $this->loadRoutes($router);

        if ($this->app->runningInConsole()) {
            // Load migrations
            $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

            // Publish Resources
            $this->publishResources();
        }

        // Override session guard
        $this->overrideSessionGuard();

        // Register event handlers
        $this->app['events']->subscribe(GenericHandler::class);

        // Share current user instance with all views
        $this->app['view']->composer('*', function ($view) {
            $view->with('currentUser', auth()->user());
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
        if (! $this->app->routesAreCached() && file_exists(base_path('routes/web.rinvex.fort.php'))) {
            $router->middleware('web')
                 ->namespace($this->getAppNamespace().'Http\Controllers')
                 ->group(base_path('routes/web.rinvex.fort.php'));

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

    /**
     * Override middleware.
     *
     * @param \Illuminate\Routing\Router $router
     *
     * @return void
     */
    protected function overrideMiddleware(Router $router)
    {
        // Append middleware to the 'web' middlware group
        $router->pushMiddlewareToGroup('web', Abilities::class);
        $router->pushMiddlewareToGroup('web', UpdateLastActivity::class);

        // Override route middleware on the fly
        $router->aliasMiddleware('auth', Authenticate::class);
        $router->aliasMiddleware('nohttpcache', NoHttpCache::class);
        $router->aliasMiddleware('guest', RedirectIfAuthenticated::class);
    }

    /**
     * Override exception handler.
     *
     * @return void
     */
    protected function overrideExceptionHandler()
    {
        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \Rinvex\Fort\Handlers\ExceptionHandler::class
        );
    }
}
