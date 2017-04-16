<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasswordResetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('auth.passwords.'.config('auth.defaults.passwords').'.table'), function (Blueprint $table) {
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
        Schema::dropIfExists(config('auth.passwords.'.config('auth.defaults.passwords').'.table'));
    }
}
