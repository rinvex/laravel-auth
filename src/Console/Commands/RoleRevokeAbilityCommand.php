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

class RoleRevokeAbilityCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fort:role:revokeability
                            {role? : The role identifier}
                            {ability? : The ability identifier}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revoke a ability from a role.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $roleField = $this->argument('role') ?: $this->ask(Lang::get('rinvex.fort::artisan.role.identifier'));

        if (intval($roleField)) {
            $role = $this->laravel['rinvex.fort.role']->find($roleField);
        } else {
            $role = $this->laravel['rinvex.fort.role']->findWhere(['slug' => $roleField])->first();
        }

        if (! $role) {
            return $this->error(Lang::get('rinvex.fort::artisan.role.invalid', ['field' => $roleField]));
        }

        $abilityField = $this->argument('ability') ?: $this->anticipate(Lang::get('rinvex.fort::artisan.role.ability'), $this->laravel['rinvex.fort.ability']->findAll()->lists('slug', 'id')->toArray());

        if (intval($abilityField)) {
            $ability = $this->laravel['rinvex.fort.ability']->find($abilityField);
        } else {
            $ability = $this->laravel['rinvex.fort.ability']->findWhere(['slug' => $abilityField])->first();
        }

        if (! $ability) {
            return $this->error(Lang::get('rinvex.fort::artisan.ability.invalid', ['field' => $abilityField]));
        }

        // Revoke role ability to..
        $role->revokeAbilityTo($ability);

        $this->info(Lang::get('rinvex.fort::artisan.role.abilityrevoked', ['role' => $role->id, 'ability' => $ability->id]));
    }
}
