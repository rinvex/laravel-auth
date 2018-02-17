<?php

declare(strict_types=1);

namespace Rinvex\Auth\Console\Commands;

use Illuminate\Console\Command;

class PublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rinvex:publish:auth {--force : Overwrite any existing files.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish Rinvex Auth Resources.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->warn($this->description);

        $this->call('vendor:publish', ['--tag' => 'rinvex-auth-config', '--force' => $this->option('force')]);
    }
}
