<?php

declare(strict_types=1);

namespace Rinvex\Fort\Traits;

use Exception;

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

        $this->warn('Seeding: '.str_after($seeder, $this->laravel->basePath().'/'));

        // Create new abilities
        foreach (json_decode(file_get_contents($seeder), true) as $ability) {
            app('rinvex.fort.ability')->firstOrCreate(array_except($ability, ['name']), array_only($ability, ['name']));
        }

        $this->info('Seeded: '.str_after($seeder, $this->laravel->basePath().'/'));
    }
}
