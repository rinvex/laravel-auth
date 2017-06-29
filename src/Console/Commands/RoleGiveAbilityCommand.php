<?php

declare(strict_types=1);

namespace Rinvex\Fort\Console\Commands;

use Rinvex\Fort\Models\Role;
use Illuminate\Console\Command;
use Rinvex\Fort\Models\Ability;

class RoleGiveAbilityCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fort:role:giveability
                            {role? : The role identifier}
                            {ability? : The ability identifier}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Give a ability from a role.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $roleField = $this->argument('role') ?: $this->ask(trans('rinvex.fort::artisan.role.identifier'));

        if ((int) $roleField) {
            $role = Role::find($roleField);
        } else {
            $role = Role::where(['slug' => $roleField])->first();
        }

        if (! $role) {
            return $this->error(trans('rinvex.fort::artisan.role.invalid', ['field' => $roleField]));
        }

        $abilityField = $this->argument('ability') ?: $this->anticipate(trans('rinvex.fort::artisan.role.ability'), Ability::all()->pluck('slug', 'id')->toArray());

        if ((int) $abilityField) {
            $ability = Ability::find($abilityField);
        } else {
            $ability = Ability::where(['slug' => $abilityField])->first();
        }

        if (! $ability) {
            return $this->error(trans('rinvex.fort::artisan.ability.invalid', ['field' => $abilityField]));
        }

        // Give role ability to..
        $role->giveAbilityTo($ability);

        $this->info(trans('rinvex.fort::artisan.role.abilitygived', ['role' => $role->id, 'ability' => $ability->id]));
    }
}
