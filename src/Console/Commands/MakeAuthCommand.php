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

namespace Rinvex\Fort\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\DetectsApplicationNamespace;

class MakeAuthCommand extends Command
{
    use DetectsApplicationNamespace;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:auth {--views : Only scaffold the authentication views}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold basic login and registration views and routes';

    /**
     * The controllers that need to be exported.
     *
     * @var array
     */
    protected $controllers = [

        'Backend/AbilitiesController',
        'Backend/DashboardController',
        'Backend/RolesController',
        'Backend/UsersController',

        'Frontend/AuthenticationController',
        'Frontend/EmailVerificationController',
        'Frontend/PasswordResetController',
        'Frontend/PhoneVerificationController',
        'Frontend/RegistrationController',
        'Frontend/SocialAuthenticationController',
        'Frontend/TwoFactorSettingsController',
        'Frontend/UserSessionsController',
        'Frontend/UserSettingsController',

        'AbstractController',
        'AuthenticatedController',
        'AuthorizedController',

    ];

    /**
     * The requests that need to be exported.
     *
     * @var array
     */
    protected $requests = [

        'Frontend/EmailVerificationRequest',
        'Frontend/PasswordResetRequest',
        'Frontend/PasswordResetSendRequest',
        'Frontend/PhoneVerificationRequest',
        'Frontend/PhoneVerificationSendRequest',
        'Frontend/UserAuthenticationRequest',

    ];

    /**
     * The views that need to be exported.
     *
     * @var array
     */
    protected $views = [

        'backend/abilities/form.blade',
        'backend/abilities/index.blade',
        'backend/common/confirm-modal.blade',
        'backend/common/layout.blade',
        'backend/common/layout-example.blade',
        'backend/common/pagination.blade',
        'backend/dashboard/home.blade',
        'backend/roles/form.blade',
        'backend/roles/index.blade',
        'backend/users/form.blade',
        'backend/users/index.blade',

        'frontend/alerts/error.blade',
        'frontend/alerts/success.blade',
        'frontend/alerts/warning.blade',
        'frontend/authentication/login.blade',
        'frontend/authentication/register.blade',
        'frontend/common/confirm-modal.blade',
        'frontend/common/layout.blade',
        'frontend/common/layout-example.blade',
        'frontend/passwordreset/request.blade',
        'frontend/passwordreset/reset.blade',
        'frontend/user/sessions.blade',
        'frontend/user/settings.blade',
        'frontend/user/twofactor.blade',
        'frontend/verification/email/request.blade',
        'frontend/verification/phone/request.blade',
        'frontend/verification/phone/token.blade',

    ];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->exportViews();

        if (! $this->option('views')) {
            $this->exportControllers();
            $this->exportRequests();
            $this->exportRoutes();
        }

        $this->info('Authentication scaffolding generated successfully.');
    }

    /**
     * Export the views.
     *
     * @return void
     */
    protected function exportViews()
    {
        foreach ($this->views as $view) {
            $viewFile = base_path('resources/views/'.$view.'.php');

            if (! is_dir($viewDir = dirname($viewFile))) {
                mkdir($viewDir, 0755, true);
            }

            copy(
                __DIR__.'/../../../resources/stubs/views/'.$view.'.stub',
                $viewFile
            );
        }
    }

    /**
     * Export the controllers.
     *
     * @return void
     */
    protected function exportControllers()
    {
        foreach ($this->controllers as $controller) {
            $controllerFile = app_path('Http/Controllers/'.$controller.'.php');

            if (! is_dir($controllerDir = dirname($controllerFile))) {
                mkdir($controllerDir, 0755, true);
            }

            file_put_contents(
                $controllerFile,
                $this->compileClassStub(__DIR__.'/../../../resources/stubs/controllers/'.$controller.'.stub')
            );
        }
    }

    /**
     * Export the requests.
     *
     * @return void
     */
    protected function exportRequests()
    {
        foreach ($this->requests as $request) {
            $requestFile = app_path('Http/Requests/'.$request.'.php');

            if (! is_dir($requestDir = dirname($requestFile))) {
                mkdir($requestDir, 0755, true);
            }

            file_put_contents(
                $requestFile,
                $this->compileClassStub(__DIR__.'/../../../resources/stubs/requests/'.$request.'.stub')
            );
        }
    }

    /**
     * Export the routes.
     *
     * @return void
     */
    protected function exportRoutes()
    {
        file_put_contents(
            base_path('routes/web.rinvex.fort.php'),
            file_get_contents(__DIR__.'/../../../resources/stubs/routes/web.rinvex.fort.stub')
        );
    }

    /**
     * Compiles the class stub.
     *
     * @param string $stub
     *
     * @return string
     */
    protected function compileClassStub($stub)
    {
        return str_replace('{{namespace}}', $this->getAppNamespace(), file_get_contents($stub));
    }
}
