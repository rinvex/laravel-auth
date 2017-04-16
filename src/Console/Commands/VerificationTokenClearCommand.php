<?php

declare(strict_types=1);

namespace Rinvex\Fort\Console\Commands;

use Illuminate\Console\Command;

class VerificationTokenClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fort:verify:clear {broker? : The name of the verification broker}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flush expired verification tokens';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        app('rinvex.fort.verifier')->broker($this->argument('broker'))->getRepository()->deleteExpired();

        $this->info('Expired verification tokens cleared!');
    }
}
