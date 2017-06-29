<?php

declare(strict_types=1);

namespace Rinvex\Fort\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Rinvex\Fort\Console\Commands\MakeAuthCommand;
use Rinvex\Fort\Console\Commands\RoleFindCommand;
use Rinvex\Fort\Console\Commands\UserFindCommand;
use Rinvex\Fort\Console\Commands\RoleCreateCommand;
use Rinvex\Fort\Console\Commands\RoleUpdateCommand;
use Rinvex\Fort\Console\Commands\UserCreateCommand;
use Rinvex\Fort\Console\Commands\UserRemindCommand;
use Rinvex\Fort\Console\Commands\UserUpdateCommand;
use Rinvex\Fort\Console\Commands\AbilityFindCommand;
use Rinvex\Fort\Services\PasswordResetBrokerManager;
use Rinvex\Fort\Console\Commands\AbilityCreateCommand;
use Rinvex\Fort\Console\Commands\AbilityUpdateCommand;
use Rinvex\Fort\Console\Commands\UserAssignRoleCommand;
use Rinvex\Fort\Console\Commands\UserRemoveRoleCommand;
use Rinvex\Fort\Console\Commands\RoleGiveAbilityCommand;
use Rinvex\Fort\Console\Commands\UserGiveAbilityCommand;
use Rinvex\Fort\Services\EmailVerificationBrokerManager;
use Rinvex\Fort\Console\Commands\RoleRevokeAbilityCommand;
use Rinvex\Fort\Console\Commands\UserRevokeAbilityCommand;

class FortDeferredServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [

        'AuthMake' => 'command.auth.make',

        'AbilityFind' => 'command.rinvex.fort.ability.find',
        'AbilityUpdate' => 'command.rinvex.fort.ability.update',
        'AbilityCreate' => 'command.rinvex.fort.ability.create',

        'RoleFind' => 'command.rinvex.fort.role.find',
        'RoleUpdate' => 'command.rinvex.fort.role.update',
        'RoleCreate' => 'command.rinvex.fort.role.create',
        'RoleGiveAbility' => 'command.rinvex.fort.role.giveability',
        'RoleRevokeAbility' => 'command.rinvex.fort.role.revokeability',

        'UserFind' => 'command.rinvex.fort.user.find',
        'UserCreate' => 'command.rinvex.fort.user.create',
        'UserUpdate' => 'command.rinvex.fort.user.update',
        'UserRemind' => 'command.rinvex.fort.user.remind',
        'UserAssignRole' => 'command.rinvex.fort.user.assignrole',
        'UserRemoveRole' => 'command.rinvex.fort.user.removerole',
        'UserGiveAbility' => 'command.rinvex.fort.user.giveability',
        'UserRevokeAbility' => 'command.rinvex.fort.user.revokeability',

    ];

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        // Register bindings
        $this->registerPasswordBroker();
        $this->registerBladeExtensions();
        $this->registerVerificationBroker();

        // Register artisan commands
        foreach (array_keys($this->commands) as $command) {
            call_user_func_array([$this, "register{$command}Command"], []);
        }

        $this->commands(array_values($this->commands));
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
            $bladeCompiler->directive('role', function ($roles) {
                return "<?php if(auth()->user()->hasRole({$roles})): ?>";
            });
            $bladeCompiler->directive('endrole', function () {
                return '<?php endif; ?>';
            });

            // @hasrole('writer') / @hasrole(['writer', 'editor'])
            $bladeCompiler->directive('hasrole', function ($roles) {
                return "<?php if(auth()->user()->hasRole({$roles})): ?>";
            });
            $bladeCompiler->directive('endhasrole', function () {
                return '<?php endif; ?>';
            });

            // @hasanyrole(['writer', 'editor'])
            $bladeCompiler->directive('hasanyrole', function ($roles) {
                return "<?php if(auth()->user()->hasAnyRole({$roles})): ?>";
            });
            $bladeCompiler->directive('endhasanyrole', function () {
                return '<?php endif; ?>';
            });

            // @hasallroles(['writer', 'editor'])
            $bladeCompiler->directive('hasallroles', function ($roles) {
                return "<?php if(auth()->user()->hasAllRoles({$roles})): ?>";
            });
            $bladeCompiler->directive('endhasallroles', function () {
                return '<?php endif; ?>';
            });
        });
    }

    /**
     * Register make auth command.
     *
     * @return void
     */
    protected function registerAuthMakeCommand()
    {
        $this->app->singleton('command.auth.make', function ($app) {
            return new MakeAuthCommand();
        });
    }

    /**
     * Register find ability command.
     *
     * @return void
     */
    protected function registerAbilityFindCommand()
    {
        $this->app->singleton('command.rinvex.fort.ability.find', function ($app) {
            return new AbilityFindCommand();
        });
    }

    /**
     * Register ability update command.
     *
     * @return void
     */
    protected function registerAbilityUpdateCommand()
    {
        $this->app->singleton('command.rinvex.fort.ability.update', function ($app) {
            return new AbilityUpdateCommand();
        });
    }

    /**
     * Register ability create command.
     *
     * @return void
     */
    protected function registerAbilityCreateCommand()
    {
        $this->app->singleton('command.rinvex.fort.ability.create', function ($app) {
            return new AbilityCreateCommand();
        });
    }

    /**
     * Register role find command.
     *
     * @return void
     */
    protected function registerRoleFindCommand()
    {
        $this->app->singleton('command.rinvex.fort.role.find', function ($app) {
            return new RoleFindCommand();
        });
    }

    /**
     * Register role update command.
     *
     * @return void
     */
    protected function registerRoleUpdateCommand()
    {
        $this->app->singleton('command.rinvex.fort.role.update', function ($app) {
            return new RoleUpdateCommand();
        });
    }

    /**
     * Register role create command.
     *
     * @return void
     */
    protected function registerRoleCreateCommand()
    {
        $this->app->singleton('command.rinvex.fort.role.create', function ($app) {
            return new RoleCreateCommand();
        });
    }

    /**
     * Register role give ability command.
     *
     * @return void
     */
    protected function registerRoleGiveAbilityCommand()
    {
        $this->app->singleton('command.rinvex.fort.role.giveability', function ($app) {
            return new RoleGiveAbilityCommand();
        });
    }

    /**
     * Register role revoke ability command.
     *
     * @return void
     */
    protected function registerRoleRevokeAbilityCommand()
    {
        $this->app->singleton('command.rinvex.fort.role.revokeability', function ($app) {
            return new RoleRevokeAbilityCommand();
        });
    }

    /**
     * Register user find command.
     *
     * @return void
     */
    protected function registerUserFindCommand()
    {
        $this->app->singleton('command.rinvex.fort.user.find', function ($app) {
            return new UserFindCommand();
        });
    }

    /**
     * Register user create command.
     *
     * @return void
     */
    protected function registerUserCreateCommand()
    {
        $this->app->singleton('command.rinvex.fort.user.create', function ($app) {
            return new UserCreateCommand();
        });
    }

    /**
     * Register user update command.
     *
     * @return void
     */
    protected function registerUserUpdateCommand()
    {
        $this->app->singleton('command.rinvex.fort.user.update', function ($app) {
            return new UserUpdateCommand();
        });
    }

    /**
     * Register user remind command.
     *
     * @return void
     */
    protected function registerUserRemindCommand()
    {
        $this->app->singleton('command.rinvex.fort.user.remind', function ($app) {
            return new UserRemindCommand();
        });
    }

    /**
     * Register user assign role command.
     *
     * @return void
     */
    protected function registerUserAssignRoleCommand()
    {
        $this->app->singleton('command.rinvex.fort.user.assignrole', function ($app) {
            return new UserAssignRoleCommand();
        });
    }

    /**
     * Register user remove role command.
     *
     * @return void
     */
    protected function registerUserRemoveRoleCommand()
    {
        $this->app->singleton('command.rinvex.fort.user.removerole', function ($app) {
            return new UserRemoveRoleCommand();
        });
    }

    /**
     * Register user give ability command.
     *
     * @return void
     */
    protected function registerUserGiveAbilityCommand()
    {
        $this->app->singleton('command.rinvex.fort.user.giveability', function ($app) {
            return new UserGiveAbilityCommand();
        });
    }

    /**
     * Register user revoke ability command.
     *
     * @return void
     */
    protected function registerUserRevokeAbilityCommand()
    {
        $this->app->singleton('command.rinvex.fort.user.revokeability', function ($app) {
            return new UserRevokeAbilityCommand();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array_merge(array_values($this->commands), [
            'auth.password',
            'rinvex.fort.emailverification',
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
        ]);
    }
}
