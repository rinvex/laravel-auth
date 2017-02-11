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

class CreateRoleUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('rinvex.fort.tables.role_user'), function (Blueprint $table) {
            // Columns
            $table->integer('role_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->timestamps();

            // Indexes
            $table->primary([
                'role_id',
                'user_id',
            ]);
            $table->foreign('role_id')
                  ->references('id')
                  ->on(config('rinvex.fort.tables.roles'))
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreign('user_id')
                  ->references('id')
                  ->on(config('rinvex.fort.tables.users'))
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

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
        Schema::drop(config('rinvex.fort.tables.role_user'));
    }
}
