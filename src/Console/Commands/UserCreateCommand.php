<?php

declare(strict_types=1);

namespace Rinvex\Fort\Console\Commands;

use Rinvex\Fort\Models\User;
use Illuminate\Console\Command;
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
            'email' => $this->argument('email') ?: $this->ask(trans('rinvex.fort::artisan.user.email')),
            'username' => $this->argument('username') ?: $this->ask(trans('rinvex.fort::artisan.user.username')),
            'password' => $this->argument('password') ?: $this->secret(trans('rinvex.fort::forms.common.password')),

            // Optional user attributes
            'first_name' => $this->argument('firstName'),
            'middle_name' => $this->argument('middleName'),
            'last_name' => $this->argument('lastName'),
            'is_active' => ! $this->option('inactive'),
            'email_verified' => ! $this->option('unverified'),

        ], [
            $this,
            'filter',
        ]);

        $rules = [
            'email' => 'required|email|min:3|max:150|unique:'.config('rinvex.fort.tables.users').',email',
            'username' => 'required|alpha_dash|min:3|max:150|unique:'.config('rinvex.fort.tables.users').',username',
        ];

        $validator = app(Factory::class)->make($data, $rules);

        if ($validator->fails()) {
            $this->error('Errors:');

            foreach ($validator->errors()->getMessages() as $key => $messages) {
                $this->error('- '.$key.': '.$messages[0]);
            }
        } else {
            $user = User::create($data);

            $this->info(trans('rinvex.fort::artisan.user.created').' ['.trans('rinvex.fort::artisan.user.id').': '.$user->id.', '.trans('rinvex.fort::artisan.user.email').': '.$user->email.', '.trans('rinvex.fort::artisan.user.username').': '.$user->username.']');
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
