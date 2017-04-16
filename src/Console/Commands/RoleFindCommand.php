<?php

declare(strict_types=1);

namespace Rinvex\Fort\Console\Commands;

use Rinvex\Fort\Models\Role;
use Illuminate\Console\Command;

class RoleFindCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fort:role:find {field? : Get specific role by field}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List matched roles.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $columns = ['id', 'name', 'slug', 'created_at', 'updated_at'];

        // Find single role
        if ($field = $this->argument('field')) {
            if ((int) $field && $role = Role::find($field, $columns)) {
                return $this->table($columns, [$role->toArray()]);
            } elseif ($role = Role::where(['slug' => $field], $columns)->first()) {
                return $this->table($columns, $role->toArray());
            }

            return $this->error(trans('rinvex.fort::artisan.role.invalid', ['field' => $field]));
        }

        // Find multiple roles
        $field = $this->anticipate(trans('rinvex.fort::artisan.role.field'), ['id', 'slug'], 'id');
        $operator = $this->anticipate(trans('rinvex.fort::artisan.role.operator'), ['=', '<', '>', '<=', '>=', '<>', '!=', 'like', 'like binary', 'not like', 'between', 'ilike', '&', '|', '^', '<<', '>>', 'rlike', 'regexp', 'not regexp', '~', '~*', '!~', '!~*', 'similar to', 'not similar to'], '=');
        $value = $this->ask(trans('rinvex.fort::artisan.role.value'));
        $results = Role::where($field, $operator, $value)->get($columns);

        return $this->table($columns, $results->toArray());
    }
}
