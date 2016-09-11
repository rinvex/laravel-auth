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
use Rinvex\Fort\Services\BrokerManager;
use Rinvex\Fort\Listeners\FortEventListener;
use Illuminate\View\Compilers\BladeCompiler;
use Rinvex\Fort\Repositories\UserRepository;
use Rinvex\Fort\Repositories\RoleRepository;
use Laravel\Socialite\SocialiteServiceProvider;
use Rinvex\Fort\Repositories\AbilityRepository;
use Rinvex\Support\Providers\BaseServiceProvider;
use Rinvex\Fort\Repositories\PersistenceRepository;
use Rinvex\Fort\Contracts\AbilityRepositoryContract;

class FortServiceProvider extends BaseServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(realpath(__DIR__.'/../../config/config.php'), 'rinvex.fort');

        // Register bindings
        $this->registerRepositories();
        $this->registerBrokerManagers();
        $this->registerBladeExtensions();

        // Register the event listener
        $this->app->bind('rinvex.fort.listener', FortEventListener::class);

        // Register Socialite Service Provider
        $this->app->register(SocialiteServiceProvider::class);
    }

    /**
     * {@inheritdoc}
     */
    public function boot(AbilityRepositoryContract $abilityRepository, Router $router)
    {
        // Publish Resources
        $this->publishResources();

        // Load routes
        $this->loadRoutes($router);

        // Load views
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'rinvex.fort');

        // Load language phrases
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', 'rinvex.fort');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        // Subscribe the registered event listener
        $this->app['events']->subscribe('rinvex.fort.listener');

        // Add custom user provider / Override default 'eloquent' user provider
        $this->app['auth']->provider('eloquent', function ($app, array $config) {
            // Return an instance of Rinvex\Fort\Contracts\UserRepositoryContract
            return $this->app['rinvex.fort.user'];
        });

        // Add custom session guard / Override default 'session' guard driver
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

        // Share current user instance with all views
        $this->app['view']->composer('*', function ($view) {
            $view->with('currentUser', Auth::user());
        });
    }

    /**
     * Bind the repositories into the IoC.
     *
     * @return void
     */
    protected function registerRepositories()
    {
        $this->bindAndAlias('rinvex.fort.role', RoleRepository::class);
        $this->bindAndAlias('rinvex.fort.user', UserRepository::class);
        $this->bindAndAlias('rinvex.fort.ability', AbilityRepository::class);
        $this->bindAndAlias('rinvex.fort.persistence', PersistenceRepository::class);
    }

    /**
     * Register the broker managers.
     *
     * @return void
     */
    protected function registerBrokerManagers()
    {
        // Register reset broker manager
        $this->app->singleton('rinvex.fort.resetter', function ($app) {
            return new BrokerManager($app, 'reset');
        });

        // Register verification broker manager
        $this->app->singleton('rinvex.fort.verifier', function ($app) {
            return new BrokerManager($app, 'verification');
        });
    }

    /**
     * Register the blade extensions.
     *
     * @return void
     */
    protected function registerBladeExtensions()
    {
        $this->app->afterResolving('blade.compiler', function (BladeCompiler $bladeCompiler) {

            // @role('writer')
            $bladeCompiler->directive('role', function ($role) {
                return "<?php if(auth()->check() && auth()->user()->hasRole({$role})): ?>";
            });
            $bladeCompiler->directive('endrole', function () {
                return '<?php endif; ?>';
            });

            // @hasrole('writer')
            $bladeCompiler->directive('hasrole', function ($role) {
                return "<?php if(auth()->check() && auth()->user()->hasRole({$role})): ?>";
            });
            $bladeCompiler->directive('endhasrole', function () {
                return '<?php endif; ?>';
            });

            // @hasanyrole(['writer', 'editor'])
            $bladeCompiler->directive('hasanyrole', function ($roles) {
                return "<?php if(auth()->check() && auth()->user()->hasAnyRole({$roles})): ?>";
            });
            $bladeCompiler->directive('endhasanyrole', function () {
                return '<?php endif; ?>';
            });

            // @hasallroles(['writer', 'editor'])
            $bladeCompiler->directive('hasallroles', function ($roles) {
                return "<?php if(auth()->check() && auth()->user()->hasAllRoles({$roles})): ?>";
            });
            $bladeCompiler->directive('endhasallroles', function () {
                return '<?php endif; ?>';
            });
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
            require __DIR__.'/../../routes/web.frontend.php';

            $this->app->booted(function () use ($router) {
                $router->getRoutes()->refreshNameLookups();
            });
        }
    }
}
