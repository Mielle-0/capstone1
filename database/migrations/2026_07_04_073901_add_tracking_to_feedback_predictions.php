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
        Schema::table('feedback_predictions', function (Blueprint $table) {
            
            $table->dropColumn('verified_dept_id');

            // 2. Add the Multi-Label JSON Column (Objective 1.3.2.2)
            $table->json('verified_dept_ids')->nullable()->after('model_version')
                  ->comment('Stores an array of verified department IDs (e.g., [1, 4])');

            // 3. Category Prediction Data (Objective 1.3.2.1)
            $table->string('category_model_version')->nullable()->after('verified_dept_ids');
            $table->string('predicted_category')->nullable()->after('category_model_version');
            $table->decimal('category_confidence', 5, 4)->nullable()->after('predicted_category');
            $table->string('verified_category')->nullable()->after('category_confidence');

            // 4. Data Quality Context
            $table->integer('input_word_count')->nullable()->after('verified_category');

            // 5. Basis of Intervention (Objective 1.3.2.3)
            $table->boolean('requires_intervention')->default(false)->after('input_word_count')
                  ->comment('True if confidence falls below threshold');
            $table->decimal('threshold_applied', 5, 4)->nullable()->after('requires_intervention');

            // 6. Capture Action Taken (Objective 1.3.2.3)
            $table->string('action_taken')->nullable()->after('threshold_applied')
                  ->comment('e.g., auto_routed, manual_approval, manual_correction');
            $table->unsignedBigInteger('action_taken_by')->nullable()->after('action_taken')
                  ->comment('User ID of the staff member who verified/intervened');
            $table->timestamp('action_taken_at')->nullable()->after('action_taken_by');

            // 7. System Performance
            $table->integer('processing_time_ms')->nullable()->after('action_taken_at');

            // Add foreign key for the staff member
            $table->foreign('action_taken_by')
                  ->references('usr_id')
                  ->on('users') // Change 'users' if your staff table has a different name
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feedback_predictions', function (Blueprint $table) {
            
            // 1. Drop Foreign Keys first
            $table->dropForeign(['action_taken_by']);
            
            // 2. Drop all newly added columns
            $table->dropColumn([
                'verified_dept_ids',
                'category_model_version',
                'predicted_category',
                'category_confidence',
                'verified_category',
                'input_word_count',
                'requires_intervention',
                'threshold_applied',
                'action_taken',
                'action_taken_by',
                'action_taken_at',
                'processing_time_ms'
            ]);

            // 3. Re-add the old column and foreign key
            $table->unsignedBigInteger('verified_dept_id')->nullable()->after('model_version');
            
            $table->foreign('verified_dept_id')
                  ->references('dep_id')
                  ->on('departments')
                  ->onDelete('set null');
        });
    }
};
