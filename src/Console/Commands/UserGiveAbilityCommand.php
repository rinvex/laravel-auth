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
use Illuminate\Support\Facades\Lang;

class UserGiveAbilityCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fort:user:giveability
                            {user? : The user identifier}
                            {ability? : The ability identifier}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Give a ability from a user.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $userField = $this->argument('user') ?: $this->ask(Lang::get('rinvex.fort::artisan.user.identifier'));

        if (intval($userField)) {
            $user = $this->laravel['rinvex.fort.user']->find($userField);
        } elseif (filter_var($userField, FILTER_VALIDATE_EMAIL)) {
            $user = $this->laravel['rinvex.fort.user']->findWhere(['email' => $userField])->first();
        } else {
            $user = $this->laravel['rinvex.fort.user']->findWhere(['username' => $userField])->first();
        }

        if (! $user) {
            return $this->error(Lang::get('rinvex.fort::artisan.user.invalid', ['field' => $userField]));
        }

        $abilityField = $this->argument('ability') ?: $this->anticipate(Lang::get('rinvex.fort::artisan.user.ability'), $this->laravel['rinvex.fort.ability']->findAll()->lists('slug', 'id')->toArray());

        if (intval($abilityField)) {
            $ability = $this->laravel['rinvex.fort.ability']->find($abilityField);
        } else {
            $ability = $this->laravel['rinvex.fort.ability']->findWhere(['slug' => $abilityField])->first();
        }

        if (! $ability) {
            return $this->error(Lang::get('rinvex.fort::artisan.ability.invalid', ['field' => $abilityField]));
        }

        // Give user ability to..
        $user->giveAbilityTo($ability);

        $this->info(Lang::get('rinvex.fort::artisan.user.abilitygived', ['user' => $user->id, 'ability' => $ability->id]));
    }
}
