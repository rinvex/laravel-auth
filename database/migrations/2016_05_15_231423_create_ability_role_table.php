<?php

declare(strict_types=1);

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
            $table->primary(['ability_id', 'role_id']);
            $table->foreign('ability_id')->references('id')->on(config('rinvex.fort.tables.abilities'))
                  ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('role_id')->references('id')->on(config('rinvex.fort.tables.roles'))
                  ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('rinvex.fort.tables.ability_role'));
    }
}
