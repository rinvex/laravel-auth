<?php

declare(strict_types=1);

namespace Rinvex\Fort\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Rinvex\Fort\Console\Commands\PublishCommand;
use Rinvex\Fort\Services\PasswordResetBrokerManager;
use Rinvex\Fort\Services\EmailVerificationBrokerManager;

class FortServiceProvider extends ServiceProvider
{
    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        PublishCommand::class => 'command.rinvex.fort.publish',
    ];

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(realpath(__DIR__.'/../../config/config.php'), 'rinvex.fort');

        // Register console commands
        ! $this->app->runningInConsole() || $this->registerCommands();

        // Register the password reset broker manager
        $this->app->singleton('auth.password', function ($app) {
            return new PasswordResetBrokerManager($app);
        });

        // Register the verification broker manager
        $this->app->singleton('rinvex.fort.emailverification', function ($app) {
            return new EmailVerificationBrokerManager($app);
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
        ! $this->app->runningInConsole() || $this->publishes([realpath(__DIR__.'/../../config/config.php') => config_path('rinvex.fort.php')], 'rinvex-fort-config');
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
            $this->app->singleton($value, $key);
        }

        $this->commands(array_values($this->commands));
    }
}
