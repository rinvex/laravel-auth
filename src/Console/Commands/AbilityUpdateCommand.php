<?php

declare(strict_types=1);

namespace Rinvex\Fort\Console\Commands;

use Illuminate\Console\Command;
use Rinvex\Fort\Models\Ability;
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
            'name' => $this->option('name'),
            'slug' => $this->option('slug'),
            'description' => $this->option('description'),

        ], [
            $this,
            'filter',
        ]);

        // Get required argument
        $field = $this->argument('field') ?: $this->ask(trans('rinvex.fort::artisan.ability.invalid'));

        // Find single ability
        if ((int) $field) {
            $ability = Ability::find($field);
        } else {
            $ability = Ability::where(['slug' => $field])->first();
        }

        if (! $ability) {
            return $this->error(trans('rinvex.fort::artisan.ability.invalid', ['field' => $field]));
        }

        $rules = [
            'name' => 'sometimes|required|max:150',
            'slug' => 'sometimes|required|alpha_dash|max:150|unique:'.config('rinvex.fort.tables.abilities'),
        ];

        if (! empty($data)) {
            $validator = app(Factory::class)->make($data, $rules);

            if ($validator->fails()) {
                $this->error('Errors:');

                foreach ($validator->errors()->getMessages() as $key => $messages) {
                    $this->error('- '.$key.': '.$messages[0]);
                }
            } else {
                $ability->fill($data)->save();

                $this->info(trans('rinvex.fort::artisan.ability.updated').' ['.trans('rinvex.fort::artisan.ability.id').': '.$ability->id.', '.trans('rinvex.fort::artisan.ability.name').': '.$ability->name.', '.trans('rinvex.fort::artisan.ability.slug').': '.$ability->slug.']');
            }
        } else {
            $this->info(trans('rinvex.fort::artisan.ability.nothing'));
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
