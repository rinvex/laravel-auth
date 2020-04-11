<?php

declare(strict_types=1);

namespace Rinvex\Auth\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Rinvex\Support\Traits\ConsoleTools;
use Rinvex\Auth\Console\Commands\PublishCommand;
use Rinvex\Auth\Services\PasswordResetBrokerManager;
use Rinvex\Auth\Services\EmailVerificationBrokerManager;

class AuthServiceProvider extends ServiceProvider
{
    use ConsoleTools;

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        PublishCommand::class => 'command.rinvex.auth.publish',
    ];

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(realpath(__DIR__.'/../../config/config.php'), 'rinvex.auth');

        // Register console commands
        $this->registerCommands($this->commands);

        // Register the password reset broker manager
        $this->app->singleton('auth.password', function ($app) {
            return new PasswordResetBrokerManager($app);
        });

        // Register the verification broker manager
        $this->app->singleton('rinvex.auth.emailverification', function ($app) {
            return new EmailVerificationBrokerManager($app);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Router $router)
    {
        // Publish resources
        $this->publishesConfig('rinvex/laravel-auth');
    }
}
