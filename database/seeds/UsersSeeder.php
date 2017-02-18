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

use Carbon\Carbon;
use Rinvex\Fort\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
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
		
		$this->command->info('Truncating table: '.config('rinvex.fort.tables.users'));
		
		$this->{$this->before.$connection}();
		
		$this->command->info('Creating admin user');

        $user = [
            'username' => 'Fort',
            'email' => 'help@rinvex.com',
            'email_verified' => true,
            'email_verified_at' => Carbon::now(),
            'remember_token' => str_random(10),
            'password' => $password = str_random(),
            'active' => true,
        ];

        $user = User::create($user);

        if (isset($this->command)) {
            $this->command->getOutput()->writeln("<comment>Username</comment>: {$user['username']} / <comment>Password</comment>: {$password}");
        }

        // Assign roles to users
        $user->assignRoles('admin');

        $this->{$this->after.$connection}();
    }
    
    /**
	 * Defines the post actions before the seed has been executed
	 *
	 * @return void
	 */
	protected function before_mysql() {
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		DB::table(config('rinvex.fort.tables.users'))->truncate();
	}
	
	/**
	 * Defines the post actions before the seed has been executed
	 *
	 * @return void
	 */
	protected function before_sqlite() {
		DB::table(config('rinvex.fort.tables.users'))->truncate();
	}
	
	/**
	 * Defines the post actions before the seed has been executed
	 *
	 * @return void
	 */
	protected function before_pgsql() {
		$tables = [
			config('rinvex.fort.tables.users')
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
