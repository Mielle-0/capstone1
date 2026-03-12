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
        Schema::create('thematic_values', function (Blueprint $table) {
            $table->id('thm_id');
            $table->unsignedBigInteger('typ_id')->nullable();
            $table->string('thm_value')->nullable();
            $table->tinyInteger('thm_active')->default(1)->nullable();

            $table->foreign('typ_id')->references('typ_id')->on('feedback_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thematic_values');
    }
};
