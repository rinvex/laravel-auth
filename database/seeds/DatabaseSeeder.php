<?php

declare(strict_types=1);

namespace Rinvex\Fort\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        Model::unguard();

        // WARNING: This action will delete all users/roles/abilities data (Can NOT be UNDONE!)
        if ($this->isFirstRun() || $this->command->confirm('WARNING! Your database already have data, this action will delete all users/roles/abilities (Can NOT be UNDONE!). Are you sure you want to continue?')) {
            Schema::disableForeignKeyConstraints();
            DB::table(config('rinvex.fort.tables.abilities'))->truncate();
            DB::table(config('rinvex.fort.tables.roles'))->truncate();
            DB::table(config('rinvex.fort.tables.users'))->truncate();
            DB::table(config('rinvex.fort.tables.ability_user'))->truncate();
            DB::table(config('rinvex.fort.tables.role_user'))->truncate();
            DB::table(config('rinvex.fort.tables.ability_role'))->truncate();
            DB::table(config('rinvex.fort.tables.socialites'))->truncate();
            Schema::enableForeignKeyConstraints();

            // Insert new data
            $this->call(AbilitiesSeeder::class);
            $this->call(RolesSeeder::class);
            $this->call(UsersSeeder::class);
        }

        Model::reguard();
    }

    protected function isFirstRun()
    {
        $userCount = app('rinvex.fort.user')->count();
        $roleCount = app('rinvex.fort.role')->count();
        $abilityCount = app('rinvex.fort.ability')->count();

        return ! $userCount && ! $roleCount && ! $abilityCount;
    }
}
