<?php

declare(strict_types=1);

namespace Rinvex\Fort\Console\Commands;

use Rinvex\Fort\Models\User;
use Illuminate\Console\Command;

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
        $userField = $this->argument('user') ?: $this->ask(trans('rinvex.fort::artisan.user.identifier'));

        if ((int) $userField) {
            $user = User::find($userField);
        } elseif (filter_var($userField, FILTER_VALIDATE_EMAIL)) {
            $user = User::where(['email' => $userField])->first();
        } else {
            $user = User::where(['username' => $userField])->first();
        }

        if (! $user) {
            return $this->error(trans('rinvex.fort::artisan.user.invalid', ['field' => $userField]));
        }

        $actionField = $this->argument('action') ?: $this->anticipate(trans('rinvex.fort::artisan.user.action'), ['resetpassword', 'verification']);

        switch ($actionField) {
            case 'resetpassword':
                $this->laravel['rinvex.fort.resetter']->broker($this->argument('broker'))->sendResetLink(['email' => $user->email]);

                return $this->info(trans('rinvex.fort::artisan.user.resetpassword'));
                break;

            case 'verification':
                $this->laravel['rinvex.fort.verifier']->broker($this->argument('broker'))->sendVerificationLink(['email' => $user->email]);

                return $this->info(trans('rinvex.fort::artisan.user.verification'));
                break;
        }

        return $this->error(trans('rinvex.fort::artisan.user.invalidaction'));
    }
}
