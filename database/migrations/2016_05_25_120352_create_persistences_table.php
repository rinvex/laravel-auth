<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersistencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('rinvex.fort.tables.persistences'), function (Blueprint $table) {
            // Columns
            $table->string('token');
            $table->integer('user_id')->unsigned();
            $table->string('agent')->nullable();
            $table->string('ip')->nullable();
            $table->boolean('attempt')->default(0);
            $table->timestamps();

            // Indexes
            $table->primary('token');
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
        Schema::dropIfExists(config('rinvex.fort.tables.persistences'));
    }
}
