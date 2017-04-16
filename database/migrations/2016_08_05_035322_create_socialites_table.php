<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('rinvex.fort.tables.socialites'), function (Blueprint $table) {
            // Columns
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('provider');
            $table->integer('provider_uid')->unsigned();
            $table->timestamps();

            // Indexes
            $table->unique(['provider', 'provider_uid']);
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
        Schema::dropIfExists(config('rinvex.fort.tables.socialites'));
    }
}
