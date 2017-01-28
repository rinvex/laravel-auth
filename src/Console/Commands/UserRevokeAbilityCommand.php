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

use Rinvex\Fort\Models\Ability;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Lang;

class UserRevokeAbilityCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fort:user:revokeability
                            {user? : The user identifier}
                            {ability? : The ability identifier}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revoke a ability from a user.';

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

        $abilityField = $this->argument('ability') ?: $this->anticipate(Lang::get('rinvex.fort::artisan.user.ability'), Ability::all()->pluck('slug', 'id')->toArray());

        if (intval($abilityField)) {
            $ability = Ability::find($abilityField);
        } else {
            $ability = Ability::where(['slug' => $abilityField])->first();
        }

        if (! $ability) {
            return $this->error(Lang::get('rinvex.fort::artisan.ability.invalid', ['field' => $abilityField]));
        }

        // Revoke user ability to..
        $user->revokeAbilities($ability);

        $this->info(Lang::get('rinvex.fort::artisan.user.abilityrevoked', ['user' => $user->id, 'ability' => $ability->id]));
    }
}
