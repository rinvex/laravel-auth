<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('rinvex.fort.tables.email_verifications'), function (Blueprint $table) {
            // Columns
            $table->string('token');
            $table->string('email');
            $table->string('agent')->nullable();
            $table->string('ip')->nullable();
            $table->timestamp('created_at');

            // Indexes
            $table->primary('token');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('rinvex.fort.tables.email_verifications'));
    }
}
