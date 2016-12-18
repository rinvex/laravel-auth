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
            if (intval($field) && $role = $this->laravel['rinvex.fort.role']->find($field, $columns)) {
                return $this->table($columns, [$role->toArray()]);
            } elseif ($role = $this->laravel['rinvex.fort.role']->findWhere(['slug' => $field], $columns)->first()) {
                return $this->table($columns, $role->toArray());
            }

            return $this->error(Lang::get('rinvex.fort::artisan.role.invalid', ['field' => $field]));
        }

        // Find multiple roles
        $field = $this->anticipate(Lang::get('rinvex.fort::artisan.role.field'), ['id', 'slug'], 'id');
        $operator = $this->anticipate(Lang::get('rinvex.fort::artisan.role.operator'), ['=', '<', '>', '<=', '>=', '<>', '!=', 'like', 'like binary', 'not like', 'between', 'ilike', '&', '|', '^', '<<', '>>', 'rlike', 'regexp', 'not regexp', '~', '~*', '!~', '!~*', 'similar to', 'not similar to'], '=');
        $value = $this->ask(Lang::get('rinvex.fort::artisan.role.value'));
        $results = $this->laravel['rinvex.fort.role']->where($field, $operator, $value)->get($columns);

        return $this->table($columns, $results->toArray());
    }

    /**
     * Register the related console commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        foreach ($this->commands as $commandKey => $commandClass) {
            $this->app->singleton($commandKey, function ($app) use ($commandClass) {
                return new $commandClass();
            });
        }

        $this->commands(array_keys($this->commands));
    }
}
