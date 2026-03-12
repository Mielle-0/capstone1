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
        Schema::create('user_departments', function (Blueprint $table) {
            $table->id('udp_id');
            $table->unsignedBigInteger('usr_id')->nullable();
            $table->unsignedBigInteger('dep_id')->nullable();

            $table->foreign('usr_id')->references('usr_id')->on('users')->onDelete('cascade');
            $table->foreign('dep_id')->references('dep_id')->on('departments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_departments');
    }
};
