<?php

declare(strict_types=1);

namespace Rinvex\Auth\Providers;

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
        PublishCommand::class,
    ];

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        // Register console commands
        $this->commands($this->commands);

        // Register the password reset broker manager
        $this->app->singleton('auth.password', function ($app) {
            return new PasswordResetBrokerManager($app);
        });

        // Register the verification broker manager
        $this->app->singleton('rinvex.auth.emailverification', function ($app) {
            return new EmailVerificationBrokerManager($app);
        });
    }
}
