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

use Rinvex\Fort\Models\Ability;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Lang;

class AbilityFindCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fort:ability:find {field? : Get specific ability by field}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List matched abilities.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $columns = ['id', 'name', 'slug', 'created_at', 'updated_at'];

        // Find single ability
        if ($field = $this->argument('field')) {
            if (intval($field) && $ability = Ability::find($field, $columns)) {
                return $this->table($columns, [$ability->toArray()]);
            } elseif ($ability = Ability::where(['slug' => $field], $columns)->first()) {
                return $this->table($columns, $ability->toArray());
            }

            return $this->error(Lang::get('rinvex.fort::artisan.ability.invalid', ['field' => $field]));
        }

        // Find multiple abilities
        $field = $this->anticipate(Lang::get('rinvex.fort::artisan.ability.field'), ['id', 'slug'], 'id');
        $operator = $this->anticipate(Lang::get('rinvex.fort::artisan.ability.operator'), ['=', '<', '>', '<=', '>=', '<>', '!=', 'like', 'like binary', 'not like', 'between', 'ilike', '&', '|', '^', '<<', '>>', 'rlike', 'regexp', 'not regexp', '~', '~*', '!~', '!~*', 'similar to', 'not similar to'], '=');
        $value = $this->ask(Lang::get('rinvex.fort::artisan.ability.value'));
        $results = Ability::where($field, $operator, $value)->get($columns);

        return $this->table($columns, $results->toArray());
    }
}
