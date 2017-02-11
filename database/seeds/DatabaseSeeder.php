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

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        Model::unguard();

        $this->call(AbilitiesSeeder::class);
        $this->call(RolesSeeder::class);
        $this->call(UsersSeeder::class);

        Model::reguard();
    }
}
