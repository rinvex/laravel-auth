<?php

declare(strict_types=1);

namespace Rinvex\Fort\Console\Commands;

use Rinvex\Fort\Models\User;
use Illuminate\Console\Command;

class UserFindCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fort:user:find {field? : Get specific user by field}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List matched users.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $columns = ['id', 'email', 'username', 'gender', 'is_active', 'verified', 'created_at', 'updated_at'];

        // Find single user
        if ($field = $this->argument('field')) {
            if ((int) $field && $user = User::find($field, $columns)) {
                return $this->table($columns, [$user->toArray()]);
            } elseif (filter_var($field, FILTER_VALIDATE_EMAIL) && $user = User::where(['email' => $field], $columns)->first()) {
                return $this->table($columns, [$user->toArray()]);
            } elseif ($user = User::where(['username' => $field], $columns)->first()) {
                return $this->table($columns, $user->toArray());
            }

            return $this->error(trans('rinvex.fort::artisan.user.invalid', ['field' => $field]));
        }

        // Find multiple users
        $field = $this->anticipate(trans('rinvex.fort::artisan.user.field'), ['id', 'email', 'username'], 'id');
        $operator = $this->anticipate(trans('rinvex.fort::artisan.user.operator'), ['=', '<', '>', '<=', '>=', '<>', '!=', 'like', 'like binary', 'not like', 'between', 'ilike', '&', '|', '^', '<<', '>>', 'rlike', 'regexp', 'not regexp', '~', '~*', '!~', '!~*', 'similar to', 'not similar to'], '=');
        $value = $this->ask(trans('rinvex.fort::artisan.user.value'));
        $results = User::where($field, $operator, $value)->get($columns);

        return $this->table($columns, $results->toArray());
    }
}
