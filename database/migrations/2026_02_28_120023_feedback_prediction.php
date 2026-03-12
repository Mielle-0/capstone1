<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * php artisan migrate:refresh --path=/database/migrations/2026_02_26_120023_feedback_prediction.php
     * php artisan migrate --path=/app/database/migrations/2026_02_26_120023_feedback_prediction.php
     */
    public function up(): void 
    {
        // 1. Ensure the table is clean if a previous attempt failed
        Schema::dropIfExists('prediction_candidates');
        Schema::dropIfExists('feedback_predictions');

        // Parent Table
        Schema::create('feedback_predictions', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('fbk_id')->unique();
            
            // Model Metadata
            $table->string('model_version');
            
            // Corrected department
            $table->unsignedBigInteger('verified_dept_id')->nullable(); 

            $table->timestamps();

            // Foreign Keys
            $table->foreign('fbk_id')
                ->references('fbk_id')
                ->on('feedbacks')
                ->onDelete('cascade');

            $table->foreign('verified_dept_id')
                ->references('dep_id')
                ->on('departments')
                ->onDelete('set null');
        });

        // Child Table (The "Simplification" of JSON)
        Schema::create('prediction_candidates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prediction_id');
            $table->unsignedInteger('dep_id'); // Links to your departments.dep_id
            $table->decimal('probability', 5, 4);
            $table->integer('rank'); // 1, 2, or 3

            $table->foreign('prediction_id')
                ->references('id')
                ->on('feedback_predictions')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_predictions');
        Schema::dropIfExists('prediction_candidates');
    }
};
