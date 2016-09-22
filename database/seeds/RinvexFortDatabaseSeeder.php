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

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RinvexFortDatabaseSeeder extends Seeder
{
    /**
     * {@inheritDoc}
     */
    public function run()
    {
        Model::unguard();

        $this->call(RinvexFortAbilitiesTableSeeder::class);
        $this->call(RinvexFortRolesTableSeeder::class);
        $this->call(RinvexFortUsersTableSeeder::class);

        Model::reguard();
    }
}
