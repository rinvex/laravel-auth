<?php

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
