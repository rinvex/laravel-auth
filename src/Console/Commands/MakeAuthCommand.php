<?php

declare(strict_types=1);

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
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->exportViews();
        $this->exportLangs();

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
        foreach (glob(__DIR__.'/../../../resources/stubs/views/*/*/*.stub') as $view) {
            $viewFile = resource_path(str_replace('.stub', '.php', explode('/stubs/', $view)[1]));

            if (! is_dir($viewDir = dirname($viewFile))) {
                mkdir($viewDir, 0755, true);
            }

            copy($view, $viewFile);
        }
    }

    /**
     * Export the language files.
     *
     * @return void
     */
    protected function exportLangs()
    {
        foreach (glob(__DIR__.'/../../../resources/stubs/language/*/*.php') as $lang) {
            $langFile = resource_path('lang/'.explode('/language/', $lang)[1]);

            if (! is_dir($langDir = dirname($langFile))) {
                mkdir($langDir, 0755, true);
            }

            copy($lang, $langFile);
        }
    }

    /**
     * Export the controllers.
     *
     * @return void
     */
    protected function exportControllers()
    {
        $controllers = array_merge(glob(__DIR__.'/../../../resources/stubs/controllers/*/*.stub'), glob(__DIR__.'/../../../resources/stubs/controllers/*.stub'));

        foreach ($controllers as $controller) {
            $controllerFile = app_path('Http/Controllers/'.explode('/controllers/', str_replace('.stub', '.php', $controller))[1]);

            if (! is_dir($controllerDir = dirname($controllerFile))) {
                mkdir($controllerDir, 0755, true);
            }

            file_put_contents($controllerFile, $this->compileClassStub($controller));
        }
    }

    /**
     * Export the requests.
     *
     * @return void
     */
    protected function exportRequests()
    {
        foreach (glob(__DIR__.'/../../../resources/stubs/requests/*/*.stub') as $request) {
            $requestFile = app_path('Http/Requests/'.explode('/requests/', str_replace('.stub', '.php', $request))[1]);

            if (! is_dir($requestDir = dirname($requestFile))) {
                mkdir($requestDir, 0755, true);
            }

            file_put_contents($requestFile, $this->compileClassStub($request));
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
