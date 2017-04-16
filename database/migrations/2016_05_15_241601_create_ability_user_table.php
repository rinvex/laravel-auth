<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAbilityUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('rinvex.fort.tables.ability_user'), function (Blueprint $table) {
            // Columns
            $table->integer('ability_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->timestamps();

            // Indexes
            $table->primary(['ability_id', 'user_id']);
            $table->foreign('ability_id')->references('id')->on(config('rinvex.fort.tables.abilities'))
                  ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on(config('rinvex.fort.tables.users'))
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
        Schema::dropIfExists(config('rinvex.fort.tables.ability_user'));
    }
}
