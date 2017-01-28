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

namespace Rinvex\Fort\Seeds;

use Rinvex\Fort\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table(config('rinvex.fort.tables.roles'))->truncate();

        // Get roles data
        $roles = json_decode(file_get_contents(__DIR__.'/../../resources/data/roles.json'), true);

        // Create new roles
        foreach ($roles as $role) {
            Role::create($role);
        }

        // Grant abilities to roles
        Role::where('slug', 'admin')->first()->grantAbilities('superadmin', 'global');

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
