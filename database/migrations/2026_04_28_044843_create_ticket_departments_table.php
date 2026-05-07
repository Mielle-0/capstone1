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
        Schema::create('ticket_departments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tck_id');
            $table->unsignedBigInteger('dep_id');
            $table->timestamps();

            $table->foreign('tck_id')->references('tck_id')->on('tickets')->onDelete('cascade');
            $table->foreign('dep_id')->references('dep_id')->on('departments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_departments');
    }
};
