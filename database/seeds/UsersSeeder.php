<?php

declare(strict_types=1);

namespace Rinvex\Fort\Seeds;

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = [
            'username' => 'Fort',
            'email' => 'help@rinvex.com',
            'email_verified' => true,
            'email_verified_at' => Carbon::now(),
            'remember_token' => str_random(10),
            'password' => $password = str_random(),
            'is_active' => true,
        ];

        $user = app('rinvex.fort.user')->create($user);

        if (isset($this->command)) {
            $this->command->getOutput()->writeln("<comment>Username</comment>: {$user['username']} / <comment>Password</comment>: {$password}");
        }

        // Assign roles to users
        $user->assignRoles('admin');
    }
}
