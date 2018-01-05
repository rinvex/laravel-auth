<?php

declare(strict_types=1);

namespace Rinvex\Fort\Providers;

use Illuminate\Routing\Router;
use Rinvex\Fort\Guards\SessionGuard;
use Rinvex\Fort\Services\AccessGate;
use Illuminate\Support\ServiceProvider;
use Rinvex\Fort\Contracts\RoleContract;
use Rinvex\Fort\Contracts\UserContract;
use Rinvex\Fort\Handlers\GenericHandler;
use Illuminate\Support\Facades\Validator;
use Rinvex\Fort\Contracts\AbilityContract;
use Rinvex\Fort\Contracts\SessionContract;
use Rinvex\Fort\Http\Middleware\Abilities;
use Illuminate\View\Compilers\BladeCompiler;
use Rinvex\Fort\Contracts\SocialiteContract;
use Rinvex\Fort\Http\Middleware\NoHttpCache;
use Rinvex\Fort\Http\Middleware\Authenticate;
use Rinvex\Fort\Console\Commands\SeedCommand;
use Rinvex\Fort\Console\Commands\MigrateCommand;
use Rinvex\Fort\Console\Commands\PublishCommand;
use Rinvex\Fort\Console\Commands\RollbackCommand;
use Rinvex\Fort\Http\Middleware\UpdateLastActivity;
use Rinvex\Fort\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;

class FortServiceProvider extends ServiceProvider
{
    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        SeedCommand::class => 'command.rinvex.fort.seed',
        MigrateCommand::class => 'command.rinvex.fort.migrate',
        PublishCommand::class => 'command.rinvex.fort.publish',
        RollbackCommand::class => 'command.rinvex.fort.rollback',
    ];

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        // Register bindings
        $this->registerPasswordBroker();
        $this->registerVerificationBroker();

        // Register console commands
        ! $this->app->runningInConsole() || $this->registerCommands();

        // Merge config
        $this->mergeConfigFrom(realpath(__DIR__.'/../../config/config.php'), 'rinvex.fort');

        if (config('rinvex.fort.boot.override_exceptionhandler')) {
            // Override Exception Handler
            $this->overrideExceptionHandler();
        }

        // Register Access Gate Binding
        $this->registerAccessGate();

        // Bind eloquent models to IoC container
        $this->app->singleton('rinvex.fort.role', function ($app) {
            return new $app['config']['rinvex.fort.models.role']();
        });
        $this->app->alias('rinvex.fort.role', RoleContract::class);

        $this->app->singleton('rinvex.fort.ability', function ($app) {
            return new $app['config']['rinvex.fort.models.ability']();
        });
        $this->app->alias('rinvex.fort.ability', AbilityContract::class);

        $this->app->singleton('rinvex.fort.session', function ($app) {
            return new $app['config']['rinvex.fort.models.session']();
        });
        $this->app->alias('rinvex.fort.session', SessionContract::class);

        $this->app->singleton('rinvex.fort.socialite', function ($app) {
            return new $app['config']['rinvex.fort.models.socialite']();
        });
        $this->app->alias('rinvex.fort.socialite', SocialiteContract::class);

        $this->app->singleton('rinvex.fort.user', function ($app) {
            return new $app['config']['auth.providers.'.$app['config']['auth.guards.'.$app['config']['auth.defaults.guard'].'.provider'].'.model']();
        });
        $this->app->alias('rinvex.fort.user', UserContract::class);
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
        // Add country validation rule
        Validator::extend('country', function ($attribute, $value) {
            return in_array($value, array_keys(countries()));
        }, 'Country MUST be valid!');

        // Add langauge validation rule
        Validator::extend('language', function ($attribute, $value) {
            return in_array($value, array_keys(languages()));
        }, 'Language MUST be valid!');

        if (config('rinvex.fort.boot.override_middleware')) {
            // Override middlware
            $this->overrideMiddleware($router);
        }

        // Publish resources
        ! $this->app->runningInConsole() || $this->publishResources();

        // Override session guard
        $this->overrideSessionGuard();

        // Register event handlers
        $this->app['events']->subscribe(GenericHandler::class);

        // Share current user instance with all views
        $this->app['view']->composer('*', function ($view) {
            $view->with('currentUser', auth()->user());
        });

        // Register blade extensions
        $this->registerBladeExtensions();
    }

    /**
     * Publish resources.
     *
     * @return void
     */
    protected function publishResources()
    {
        $this->publishes([realpath(__DIR__.'/../../config/config.php') => config_path('rinvex.fort.php')], 'rinvex-fort-config');
        $this->publishes([realpath(__DIR__.'/../../database/migrations') => database_path('migrations')], 'rinvex-fort-migrations');
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

    /**
     * Register the password broker.
     *
     * @return void
     */
    protected function registerPasswordBroker()
    {
        $this->app->singleton('auth.password', function ($app) {
            return new PasswordResetBrokerManager($app);
        });

        $this->app->bind('auth.password.broker', function ($app) {
            return $app->make('auth.password')->broker();
        });
    }

    /**
     * Register the verification broker.
     *
     * @return void
     */
    protected function registerVerificationBroker()
    {
        $this->app->singleton('rinvex.fort.emailverification', function ($app) {
            return new EmailVerificationBrokerManager($app);
        });

        $this->app->bind('rinvex.fort.emailverification.broker', function ($app) {
            return $app->make('rinvex.fort.emailverification')->broker();
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

            // @role('writer') / @hasrole(['writer', 'editor'])
            $bladeCompiler->directive('role', function ($expression) {
                return "<?php if(auth()->user()->hasRole({$expression})): ?>";
            });
            $bladeCompiler->directive('endrole', function () {
                return '<?php endif; ?>';
            });

            // @hasrole('writer') / @hasrole(['writer', 'editor'])
            $bladeCompiler->directive('hasrole', function ($expression) {
                return "<?php if(auth()->user()->hasRole({$expression})): ?>";
            });
            $bladeCompiler->directive('endhasrole', function () {
                return '<?php endif; ?>';
            });

            // @hasanyrole(['writer', 'editor'])
            $bladeCompiler->directive('hasanyrole', function ($expression) {
                return "<?php if(auth()->user()->hasAnyRole({$expression})): ?>";
            });
            $bladeCompiler->directive('endhasanyrole', function () {
                return '<?php endif; ?>';
            });

            // @hasallroles(['writer', 'editor'])
            $bladeCompiler->directive('hasallroles', function ($expression) {
                return "<?php if(auth()->user()->hasAllRoles({$expression})): ?>";
            });
            $bladeCompiler->directive('endhasallroles', function () {
                return '<?php endif; ?>';
            });
        });
    }

    /**
     * Register console commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        // Register artisan commands
        foreach ($this->commands as $key => $value) {
            $this->app->singleton($value, function ($app) use ($key) {
                return new $key();
            });
        }

        $this->commands(array_values($this->commands));
    }
}
