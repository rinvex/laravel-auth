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

use Illuminate\Support\Str;
use Rinvex\Fort\Models\Ability;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Lang;
use Illuminate\Contracts\Validation\Factory;

class AbilityCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fort:ability:create
                            {name? : The name of the ability}
                            {slug? : The slug of the ability}
                            {--D|description= : The description of the ability}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new ability.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $data = array_filter([

            // Required ability attributes
            'name'        => $name = $this->argument('name') ?: $this->ask(Lang::get('rinvex.fort::artisan.ability.name')),
            'slug'        => $this->argument('slug') ?: Str::slug($name),

            // Optional ability attributes
            'description' => $this->option('description'),

        ]);

        $rules = [
            'name' => 'required|max:255',
            'slug' => 'required|max:255|alpha_dash|unique:'.config('rinvex.fort.tables.abilities'),
        ];

        $validator = app(Factory::class)->make($data, $rules);

        if ($validator->fails()) {
            $this->error('Errors:');

            foreach ($validator->errors()->getMessages() as $key => $messages) {
                $this->error('- '.$key.': '.$messages[0]);
            }
        } else {
            $ability = Ability::create($data);

            $this->info(Lang::get('rinvex.fort::artisan.ability.created').' ['.Lang::get('rinvex.fort::artisan.ability.id').': '.$ability->id.', '.Lang::get('rinvex.fort::artisan.ability.name').': '.$ability->name.', '.Lang::get('rinvex.fort::artisan.ability.slug').': '.$ability->slug.']');
        }
    }
}
