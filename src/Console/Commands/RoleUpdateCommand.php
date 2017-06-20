<?php

declare(strict_types=1);

namespace Rinvex\Fort\Console\Commands;

use Illuminate\Console\Command;
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
            'name' => $this->option('name'),
            'slug' => $this->option('slug'),
            'description' => $this->option('description'),

        ], [
            $this,
            'filter',
        ]);

        // Get required argument
        $field = $this->argument('field') ?: $this->ask(trans('rinvex.fort::artisan.user.invalid'));

        // Find single role
        if ((int) $field) {
            $role = Role::find($field);
        } else {
            $role = Role::where(['slug' => $field])->first();
        }

        if (! $role) {
            return $this->error(trans('rinvex.fort::artisan.role.invalid', ['field' => $field]));
        }

        $rules = [
            'name' => 'sometimes|required|max:150',
            'slug' => 'sometimes|required|alpha_dash|max:150|unique:'.config('rinvex.fort.tables.roles'),
        ];

        if (! empty($data)) {
            $validator = app(Factory::class)->make($data, $rules);

            if ($validator->fails()) {
                $this->error('Errors:');

                foreach ($validator->errors()->getMessages() as $key => $messages) {
                    $this->error('- '.$key.': '.$messages[0]);
                }
            } else {
                $role->fill($data)->save();

                $this->info(trans('rinvex.fort::artisan.role.updated').' ['.trans('rinvex.fort::artisan.role.id').': '.$role->id.', '.trans('rinvex.fort::artisan.role.name').': '.$role->name.', '.trans('rinvex.fort::artisan.role.slug').': '.$role->slug.']');
            }
        } else {
            $this->info(trans('rinvex.fort::artisan.role.nothing'));
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
