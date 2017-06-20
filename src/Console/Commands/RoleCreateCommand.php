<?php

declare(strict_types=1);

namespace Rinvex\Fort\Console\Commands;

use Illuminate\Support\Str;
use Rinvex\Fort\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Contracts\Validation\Factory;

class RoleCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fort:role:create
                            {name? : The name of the role}
                            {slug? : The slug of the role}
                            {--D|description= : The description of the role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new role.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $data = array_filter([

            // Required role attributes
            'name' => $name = $this->argument('name') ?: $this->ask(trans('rinvex.fort::artisan.role.name')),
            'slug' => $this->argument('slug') ?: Str::slug($name),

            // Optional role attributes
            'description' => $this->option('description'),

        ]);

        $rules = [
            'name' => 'required|string|max:150',
            'slug' => 'required|alpha_dash|max:150|unique:'.config('rinvex.fort.tables.roles'),
        ];

        $validator = app(Factory::class)->make($data, $rules);

        if ($validator->fails()) {
            $this->error('Errors:');

            foreach ($validator->errors()->getMessages() as $key => $messages) {
                $this->error('- '.$key.': '.$messages[0]);
            }
        } else {
            $role = Role::create($data);

            $this->info(trans('rinvex.fort::artisan.role.created').' ['.trans('rinvex.fort::artisan.role.id').': '.$role->id.', '.trans('rinvex.fort::artisan.role.name').': '.$role->name.', '.trans('rinvex.fort::artisan.role.slug').': '.$role->slug.']');
        }
    }
}
