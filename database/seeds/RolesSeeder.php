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
    protected $before   = "before_";
	protected $after    = "after_";
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $connection = config('database.default');
		
		$this->command->info('Truncating table: '.config('rinvex.fort.tables.roles'));
		
		$this->{$this->before.$connection}();
		
		$this->command->info('Creating default roles');

        // Get roles data
        $roles = json_decode(file_get_contents(__DIR__.'/../../resources/data/roles.json'), true);

        // Create new roles
        foreach ($roles as $role) {
            Role::create($role);
        }

        // Grant abilities to roles
        Role::where('slug', 'admin')->first()->grantAbilities('superadmin', 'global');

        $this->{$this->after.$connection}();
    }
    
    /**
	 * Defines the post actions before the seed has been executed
	 *
	 * @return void
	 */
	protected function before_mysql() {
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		DB::table(config('rinvex.fort.tables.roles'))->truncate();
	}
	
	/**
	 * Defines the post actions before the seed has been executed
	 *
	 * @return void
	 */
	protected function before_sqlite() {
		DB::table(config('rinvex.fort.tables.roles'))->truncate();
	}
	
	/**
	 * Defines the post actions before the seed has been executed
	 *
	 * @return void
	 */
	protected function before_pgsql() {
		$tables = [
			config('rinvex.fort.tables.roles')
		];
		
		DB::statement('TRUNCATE TABLE ' . implode(',', $tables). ' CASCADE;');
	}
	
	/**
	 * Defines the actions after the seed has been executed
	 *
	 * @return void
	 */
	protected function after_mysql() {
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}
	
	/**
	 * Defines the actions after the seed has been executed
	 *
	 * @return void
	 */
	protected function after_sqlite() {
		//nothing to do here
	}
	
	/**
	 * Defines the actions after the seed has been executed
	 *
	 * @return void
	 */
	protected function after_pgsql() {
		//nothing to do here
	}
}
