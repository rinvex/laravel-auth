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

use Rinvex\Fort\Models\Ability;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AbilitiesSeeder extends Seeder
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
		
		$this->command->info('Truncating table: '.config('rinvex.fort.tables.abilities'));
		
		$this->{$this->before.$connection}();
		
		$this->command->info('Creating permissions');

        // Get abilities data
        $abilities = json_decode(file_get_contents(__DIR__.'/../../resources/data/abilities.json'), true);

        // Create new abilities
        foreach ($abilities as $ability) {
            Ability::create($ability);
        }

		$this->{$this->after.$connection}();
    }
    
    /**
	 * Defines the post actions before the seed has been executed
	 *
	 * @return void
	 */
	protected function before_mysql() {
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		DB::table(config('rinvex.fort.tables.abilities'))->truncate();
	}
	
	/**
	 * Defines the post actions before the seed has been executed
	 *
	 * @return void
	 */
	protected function before_sqlite() {
		DB::table(config('rinvex.fort.tables.abilities'))->truncate();
	}
	
	/**
	 * Defines the post actions before the seed has been executed
	 *
	 * @return void
	 */
	protected function before_pgsql() {
		$tables = [
			config('rinvex.fort.tables.abilities')
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
