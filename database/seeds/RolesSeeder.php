<?php

declare(strict_types=1);

namespace Rinvex\Fort\Seeds;

use Rinvex\Fort\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get roles data
        $roles = json_decode(file_get_contents(__DIR__.'/../../resources/data/roles.json'), true);

        // Create new roles
        foreach ($roles as $role) {
            Role::create($role);
        }

        // Grant abilities to roles
        Role::where('slug', 'admin')->first()->grantAbilities('superadmin', 'global');
    }
}
