<?php

declare(strict_types=1);

namespace Rinvex\Fort\Traits;

use Exception;
use Rinvex\Fort\Models\Ability;

trait AbilitySeeder
{
    /**
     * Seed taggable abilities.
     *
     * @param string $seeder
     *
     * @throws \Exception
     *
     * @return void
     */
    protected function seedAbilities(string $seeder)
    {
        if (! file_exists($seeder)) {
            throw new Exception("Abilities seeder file '{$seeder}' does NOT exist!");
        }

        // Create new abilities
        foreach (json_decode(file_get_contents($seeder), true) as $ability) {
            Ability::firstOrCreate(array_except($ability, ['name']), array_only($ability, ['name']));
        }

        $this->info("Abilities seeder file '{$seeder}' seeded successfully!");
    }
}
