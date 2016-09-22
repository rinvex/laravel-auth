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
use Illuminate\Support\Facades\DB;

class RinvexFortUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table(config('rinvex.fort.tables.users'))->truncate();

        // Get users data
        $users = require __DIR__.'/../../resources/data/users.php';

        // Create new users
        foreach ($users as $user) {
            app('rinvex.fort.user')->create($user);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
