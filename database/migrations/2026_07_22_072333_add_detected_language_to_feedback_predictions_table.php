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
            $table->string('detected_language', 50)
                  ->nullable()
                  ->after('category_confidence');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feedback_predictions', function (Blueprint $table) {
            $table->dropColumn('detected_language');
        });
    }
};
