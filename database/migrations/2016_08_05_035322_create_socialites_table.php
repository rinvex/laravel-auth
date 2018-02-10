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
    public function up(): void
    {
        Schema::create(config('rinvex.fort.tables.socialites'), function (Blueprint $table) {
            // Columns
            $table->increments('id');
            $table->morphs('user');
            $table->string('provider');
            $table->string('provider_uid');
            $table->timestamps();

            // Indexes
            $table->unique(['provider', 'provider_uid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(config('rinvex.fort.tables.socialites'));
    }
}
