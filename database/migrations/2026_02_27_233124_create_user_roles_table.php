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
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id('url_id');
            $table->unsignedBigInteger('usr_id')->nullable();
            $table->unsignedBigInteger('rol_id')->nullable();

            $table->foreign('usr_id')->references('usr_id')->on('users')->onDelete('cascade');
            $table->foreign('rol_id')->references('rol_id')->on('roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};
