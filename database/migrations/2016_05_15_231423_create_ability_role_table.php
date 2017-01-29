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

class CreateAbilityRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('rinvex.fort.tables.ability_role'), function (Blueprint $table) {
            // Columns
            $table->integer('ability_id')->unsigned();
            $table->integer('role_id')->unsigned();
            $table->timestamps();

            // Indexes
            $table->primary([
                'ability_id',
                'role_id',
            ]);
            $table->foreign('ability_id')
                  ->references('id')
                  ->on(config('rinvex.fort.tables.abilities'))
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreign('role_id')
                  ->references('id')
                  ->on(config('rinvex.fort.tables.roles'))
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
        Schema::drop(config('rinvex.fort.tables.ability_role'));
    }
}
