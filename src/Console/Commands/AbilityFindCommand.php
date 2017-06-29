<?php

declare(strict_types=1);

namespace Rinvex\Fort\Console\Commands;

use Illuminate\Console\Command;
use Rinvex\Fort\Models\Ability;

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
            if ((int) $field && $ability = Ability::find($field, $columns)) {
                return $this->table($columns, [$ability->toArray()]);
            } elseif ($ability = Ability::where(['slug' => $field], $columns)->first()) {
                return $this->table($columns, $ability->toArray());
            }

            return $this->error(trans('rinvex.fort::artisan.ability.invalid', ['field' => $field]));
        }

        // Find multiple abilities
        $field = $this->anticipate(trans('rinvex.fort::artisan.ability.field'), ['id', 'slug'], 'id');
        $operator = $this->anticipate(trans('rinvex.fort::artisan.ability.operator'), ['=', '<', '>', '<=', '>=', '<>', '!=', 'like', 'like binary', 'not like', 'between', 'ilike', '&', '|', '^', '<<', '>>', 'rlike', 'regexp', 'not regexp', '~', '~*', '!~', '!~*', 'similar to', 'not similar to'], '=');
        $value = $this->ask(trans('rinvex.fort::artisan.ability.value'));
        $results = Ability::where($field, $operator, $value)->get($columns);

        return $this->table($columns, $results->toArray());
    }
}
