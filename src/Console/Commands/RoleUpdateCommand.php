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

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Lang;
use Illuminate\Contracts\Validation\Factory;

class RoleUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fort:role:update
                            {field? : The identifier of the role (id, email, rolename)}
                            {--N|name= : The name of the role}
                            {--S|slug= : The slug of the role}
                            {--D|description= : The description of the role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update an existing role.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $data = array_filter([

            // Required role attributes
            'name'        => $this->option('name'),
            'slug'        => $this->option('slug'),
            'description' => $this->option('description'),

        ], [
            $this,
            'filter',
        ]);

        // Get required argument
        $field = $this->argument('field') ?: $this->ask(Lang::get('rinvex.fort::artisan.user.invalid'));

        // Find single role
        if (intval($field)) {
            $role = Role::find($field);
        } else {
            $role = Role::where(['slug' => $field])->first();
        }

        if (! $role) {
            return $this->error(Lang::get('rinvex.fort::artisan.role.invalid', ['field' => $field]));
        }

        $rules = [
            'name' => 'sometimes|required|max:255',
            'slug' => 'sometimes|required|max:255|unique:'.config('rinvex.fort.tables.roles'),
        ];

        if (! empty($data)) {
            $validator = app(Factory::class)->make($data, $rules);

            if ($validator->fails()) {
                $this->error('Errors:');

                foreach ($validator->errors()->getMessages() as $key => $messages) {
                    $this->error('- '.$key.': '.$messages[0]);
                }
            } else {
                $role->update($data);

                $this->info(Lang::get('rinvex.fort::artisan.role.updated').' ['.Lang::get('rinvex.fort::artisan.role.id').': '.$role->id.', '.Lang::get('rinvex.fort::artisan.role.name').': '.$role->name.', '.Lang::get('rinvex.fort::artisan.role.slug').': '.$role->slug.']');
            }
        } else {
            $this->info(Lang::get('rinvex.fort::artisan.role.nothing'));
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
