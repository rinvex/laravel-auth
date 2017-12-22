<?php

declare(strict_types=1);

namespace Rinvex\Fort\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Rinvex\Fort\Console\Commands\SeedCommand;
use Rinvex\Fort\Console\Commands\MigrateCommand;
use Rinvex\Fort\Console\Commands\PublishCommand;
use Rinvex\Fort\Console\Commands\MakeAuthCommand;
use Rinvex\Fort\Console\Commands\RollbackCommand;
use Rinvex\Fort\Services\PasswordResetBrokerManager;
use Rinvex\Fort\Services\EmailVerificationBrokerManager;

class FortDeferredServiceProvider extends ServiceProvider
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
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

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
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Register blade extensions
        $this->registerBladeExtensions();
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
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'auth.password',
            'rinvex.fort.emailverification',
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
        ];
    }

    /**
     * Register console commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        if (config('rinvex.fort.boot.override_makeauth_command')) {
            $this->app->singleton('command.auth.make', function ($app) {
                return new MakeAuthCommand();
            });
            $this->commands('command.auth.make');
        }

        // Register artisan commands
        foreach ($this->commands as $key => $value) {
            $this->app->singleton($value, function ($app) use ($key) {
                return new $key();
            });
        }

        $this->commands(array_values($this->commands));
    }
}
