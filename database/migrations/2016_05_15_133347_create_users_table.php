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

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('rinvex.fort.tables.users'), function (Blueprint $table) {
            // Columns
            $table->increments('id');
            $table->string('username');
            $table->string('password');
            $table->rememberToken();
            $table->string('email');
            $table->boolean('email_verified')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('phone_verified')->default(false);
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('prefix')->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('suffix')->nullable();
            $table->string('job_title')->nullable();
            $table->string('country', 2)->nullable();
            $table->text('two_factor')->nullable();
            $table->date('birthdate')->nullable();
            $table->enum('gender', [
                'male',
                'female',
                'undisclosed',
            ])->default('undisclosed');
            $table->boolean('active')->default(true);
            $table->timestamp('login_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->unique('email');
            $table->unique('username');

            // Engine
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop(config('rinvex.fort.tables.users'));
    }
}
