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

declare(strict_types=1);

namespace Rinvex\Fort\Seeds;

use Illuminate\Database\Seeder;
use Rinvex\Fort\Models\Ability;

class AbilitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get abilities data
        $abilities = json_decode(file_get_contents(__DIR__.'/../../resources/data/abilities.json'), true);

        // Create new abilities
        foreach ($abilities as $ability) {
            Ability::create($ability);
        }
    }
}
