<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('usr_id');
            $table->string('branch_id')->default('UM-MAIN')->nullable();
            $table->string('usr_code')->nullable();
            $table->string('usr_password')->nullable();
            $table->string('usr_name')->nullable();
            $table->string('usr_mobile', 10)->nullable();
            $table->tinyInteger('usr_active')->default(1)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
