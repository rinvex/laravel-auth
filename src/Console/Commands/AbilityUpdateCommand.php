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
use Illuminate\Contracts\Validation\Factory;

class AbilityUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fort:ability:update
                            {field? : The identifier of the ability (id, email, abilityname)}
                            {--N|name= : The name of the ability}
                            {--S|slug= : The slug of the ability}
                            {--D|description= : The description of the ability}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update an existing ability.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $data = array_filter([

            // Required ability attributes
            'name'        => $this->option('name'),
            'slug'        => $this->option('slug'),
            'description' => $this->option('description'),

        ], [
            $this,
            'filter',
        ]);

        // Get required argument
        $field = $this->argument('field') ?: $this->ask(Lang::get('rinvex.fort::artisan.ability.invalid'));

        // Find single ability
        if (intval($field)) {
            $ability = Ability::find($field);
        } else {
            $ability = Ability::where(['slug' => $field])->first();
        }

        if (! $ability) {
            return $this->error(Lang::get('rinvex.fort::artisan.ability.invalid', ['field' => $field]));
        }

        $rules = [
            'name' => 'sometimes|required|max:255',
            'slug' => 'sometimes|required|max:255|unique:'.config('rinvex.fort.tables.abilities'),
        ];

        if (! empty($data)) {
            $validator = app(Factory::class)->make($data, $rules);

            if ($validator->fails()) {
                $this->error('Errors:');

                foreach ($validator->errors()->getMessages() as $key => $messages) {
                    $this->error('- '.$key.': '.$messages[0]);
                }
            } else {
                $ability->update($data);

                $this->info(Lang::get('rinvex.fort::artisan.ability.updated').' ['.Lang::get('rinvex.fort::artisan.ability.id').': '.$ability->id.', '.Lang::get('rinvex.fort::artisan.ability.name').': '.$ability->name.', '.Lang::get('rinvex.fort::artisan.ability.slug').': '.$ability->slug.']');
            }
        } else {
            $this->info(Lang::get('rinvex.fort::artisan.ability.nothing'));
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
