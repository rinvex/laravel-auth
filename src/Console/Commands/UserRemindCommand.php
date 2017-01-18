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

use Rinvex\Fort\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Lang;

class UserRemindCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fort:user:remind
                            {user? : The user identifier}
                            {action? : The action to remind user of}
                            {broker? : The name of the password broker}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remind user to take action (reset password, verify email).';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $userField = $this->argument('user') ?: $this->ask(Lang::get('rinvex.fort::artisan.user.identifier'));

        if (intval($userField)) {
            $user = User::find($userField);
        } elseif (filter_var($userField, FILTER_VALIDATE_EMAIL)) {
            $user = User::where(['email' => $userField])->first();
        } else {
            $user = User::where(['username' => $userField])->first();
        }

        if (! $user) {
            return $this->error(Lang::get('rinvex.fort::artisan.user.invalid', ['field' => $userField]));
        }

        $actionField = $this->argument('action') ?: $this->anticipate(Lang::get('rinvex.fort::artisan.user.action'), ['resetpassword', 'verification']);

        switch ($actionField) {
            case 'resetpassword':
                $this->laravel['rinvex.fort.resetter']->broker($this->argument('broker'))->sendResetLink(['email' => $user->email]);

                return $this->info(Lang::get('rinvex.fort::artisan.user.resetpassword'));
                break;

            case 'verification':
                $this->laravel['rinvex.fort.verifier']->broker($this->argument('broker'))->sendVerificationLink(['email' => $user->email]);

                return $this->info(Lang::get('rinvex.fort::artisan.user.verification'));
                break;
        }

        return $this->error(Lang::get('rinvex.fort::artisan.user.invalidaction'));
    }
}
