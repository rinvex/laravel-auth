<?php

declare(strict_types=1);

namespace Rinvex\Fort\Providers;

use Rinvex\Fort\Models\Role;
use Rinvex\Fort\Models\User;
use Illuminate\Routing\Router;
use Rinvex\Fort\Models\Ability;
use Rinvex\Fort\Models\Session;
use Rinvex\Fort\Models\Socialite;
use Rinvex\Fort\Services\AccessGate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\Compilers\BladeCompiler;
use Rinvex\Fort\Console\Commands\MigrateCommand;
use Rinvex\Fort\Console\Commands\PublishCommand;
use Rinvex\Fort\Console\Commands\RollbackCommand;
use Rinvex\Fort\Services\PasswordResetBrokerManager;
use Rinvex\Fort\Services\EmailVerificationBrokerManager;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;

class FortServiceProvider extends ServiceProvider
{
    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
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

        // Register Access Gate Binding
        $this->registerAccessGate();

        // Bind eloquent models to IoC container
        $this->app->singleton('rinvex.fort.role', function ($app) {
            return new $app['config']['rinvex.fort.models.role']();
        });
        $this->app->alias('rinvex.fort.role', Role::class);

        $this->app->singleton('rinvex.fort.ability', function ($app) {
            return new $app['config']['rinvex.fort.models.ability']();
        });
        $this->app->alias('rinvex.fort.ability', Ability::class);

        $this->app->singleton('rinvex.fort.session', function ($app) {
            return new $app['config']['rinvex.fort.models.session']();
        });
        $this->app->alias('rinvex.fort.session', Session::class);

        $this->app->singleton('rinvex.fort.socialite', function ($app) {
            return new $app['config']['rinvex.fort.models.socialite']();
        });
        $this->app->alias('rinvex.fort.socialite', Socialite::class);

        $this->app->singleton('rinvex.fort.user', function ($app) {
            return new $app['config']['auth.providers.'.$app['config']['auth.guards.'.$app['config']['auth.defaults.guard'].'.provider'].'.model']();
        });
        $this->app->alias('rinvex.fort.user', User::class);
    }

    /**
     * Register the access gate service.
     *
     * @return void
     */
    protected function registerAccessGate(): void
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

        // Publish resources
        ! $this->app->runningInConsole() || $this->publishResources();

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
    protected function publishResources(): void
    {
        $this->publishes([realpath(__DIR__.'/../../config/config.php') => config_path('rinvex.fort.php')], 'rinvex-fort-config');
        $this->publishes([realpath(__DIR__.'/../../database/migrations') => database_path('migrations')], 'rinvex-fort-migrations');
    }

    /**
     * Register the password broker.
     *
     * @return void
     */
    protected function registerPasswordBroker(): void
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
    protected function registerVerificationBroker(): void
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
    protected function registerBladeExtensions(): void
    {
        $this->app->afterResolving('blade.compiler', function (BladeCompiler $bladeCompiler) {

            // @hasAnyRoles(['writer', 'editor'])
            $bladeCompiler->directive('hasAnyRoles', function ($expression) {
                return "<?php if(auth()->user()->hasAnyRoles({$expression})): ?>";
            });
            $bladeCompiler->directive('endHasAnyRoles', function () {
                return '<?php endif; ?>';
            });

            // @hasAllRoles(['writer', 'editor'])
            $bladeCompiler->directive('hasAllRoles', function ($expression) {
                return "<?php if(auth()->user()->hasAllRoles({$expression})): ?>";
            });
            $bladeCompiler->directive('endHasAllRoles', function () {
                return '<?php endif; ?>';
            });
        });
    }

    /**
     * Register console commands.
     *
     * @return void
     */
    protected function registerCommands(): void
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
