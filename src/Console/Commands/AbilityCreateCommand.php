<?php

declare(strict_types=1);

namespace Rinvex\Fort\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Rinvex\Fort\Models\Ability;
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
            'name' => $name = $this->argument('name') ?: $this->ask(trans('rinvex.fort::artisan.ability.name')),
            'slug' => $this->argument('slug') ?: Str::slug($name),

            // Optional ability attributes
            'description' => $this->option('description'),

        ]);

        $rules = [
            'name' => 'required|string|max:150',
            'slug' => 'required|alpha_dash|max:150|unique:'.config('rinvex.fort.tables.abilities'),
        ];

        $validator = app(Factory::class)->make($data, $rules);

        if ($validator->fails()) {
            $this->error('Errors:');

            foreach ($validator->errors()->getMessages() as $key => $messages) {
                $this->error('- '.$key.': '.$messages[0]);
            }
        } else {
            $ability = Ability::create($data);

            $this->info(trans('rinvex.fort::artisan.ability.created').' ['.trans('rinvex.fort::artisan.ability.id').': '.$ability->id.', '.trans('rinvex.fort::artisan.ability.name').': '.$ability->name.', '.trans('rinvex.fort::artisan.ability.slug').': '.$ability->slug.']');
        }
    }
}
