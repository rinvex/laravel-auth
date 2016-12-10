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
        app('rinvex.fort.verifier')->broker($this->argument('broker'))->getTokenRepository()->deleteExpired();

        $this->info('Expired verification tokens cleared!');
    }
}
