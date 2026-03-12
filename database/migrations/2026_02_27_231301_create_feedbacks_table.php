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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id('fbk_id');
            $table->string('fbk_uuid', 50)->nullable();
            $table->integer('src_id')->nullable();
            $table->unsignedBigInteger('thm_id')->nullable();
            $table->unsignedBigInteger('typ_id')->nullable();
            $table->string('branch_id')->nullable();
            $table->integer('std_id_no')->nullable();
            $table->string('std_name')->nullable();
            $table->string('std_email')->nullable();
            $table->string('std_mobile', 11)->nullable();
            $table->string('std_program')->nullable();
            $table->mediumText('fbk_details')->nullable();
            $table->string('fbk_attachment')->nullable();
            $table->dateTime('fbk_date_created')->nullable();
            $table->dateTime('fbk_date_validated')->nullable();
            $table->integer('fbk_validated_by')->nullable();
            $table->integer('fbk_status')->default(0)->nullable();
            $table->mediumText('fbk_disapprove_details')->nullable();
            $table->tinyInteger('fbk_active')->default(1)->nullable();
            $table->integer('fbk_created_by')->nullable();
            $table->string('ip_address')->nullable();

            // Foreign Keys
            $table->foreign('thm_id')->references('thm_id')->on('thematic_values');
            $table->foreign('typ_id')->references('typ_id')->on('feedback_types');
            $table->foreign('branch_id')->references('branch_id')->on('branches');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
