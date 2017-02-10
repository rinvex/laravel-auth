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

namespace Rinvex\Fort\Console\Commands;

use Rinvex\Fort\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Lang;
use Illuminate\Contracts\Validation\Factory;

class UserCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fort:user:create
                            {email? : The email address of the user}
                            {username? : The username of the user}
                            {password? : The password of the user}
                            {firstName? : The first name of the user}
                            {middleName? : The middle name of the user}
                            {lastName? : The last name of the user}
                            {--I|inactive : Set the user as inactive}
                            {--U|unverified : Set the user as unverified}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $data = array_filter([

            // Required user attributes
            'email'          => $this->argument('email') ?: $this->ask(Lang::get('rinvex.fort::artisan.user.email')),
            'username'       => $this->argument('username') ?: $this->ask(Lang::get('rinvex.fort::artisan.user.username')),
            'password'       => bcrypt($this->argument('password') ?: $this->secret(Lang::get('rinvex.fort::forms.common.password'))),

            // Optional user attributes
            'first_name'     => $this->argument('firstName'),
            'middle_name'    => $this->argument('middleName'),
            'last_name'      => $this->argument('lastName'),
            'active'         => ! $this->option('inactive'),
            'email_verified' => ! $this->option('unverified'),

        ], [
            $this,
            'filter',
        ]);

        $rules = [
            'email'    => 'required|email|max:255|unique:'.config('rinvex.fort.tables.users').',email',
            'username' => 'required|max:255|unique:'.config('rinvex.fort.tables.users').',username',
        ];

        $validator = app(Factory::class)->make($data, $rules);

        if ($validator->fails()) {
            $this->error('Errors:');

            foreach ($validator->errors()->getMessages() as $key => $messages) {
                $this->error('- '.$key.': '.$messages[0]);
            }
        } else {
            $user = User::create($data);

            $this->info(Lang::get('rinvex.fort::artisan.user.created').' ['.Lang::get('rinvex.fort::artisan.user.id').': '.$user->id.', '.Lang::get('rinvex.fort::artisan.user.email').': '.$user->email.', '.Lang::get('rinvex.fort::artisan.user.username').': '.$user->username.']');
        }
    }

    /**
     * Filter null and empty values.
     *
     * @param $value
     *
     * @return bool
     */
    protected function filter($value)
    {
        return $value !== null && $value !== '';
    }
}
