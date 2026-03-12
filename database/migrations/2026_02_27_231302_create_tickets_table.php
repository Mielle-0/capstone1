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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id('tck_id');
            $table->string('tck_uuid')->nullable();
            $table->unsignedBigInteger('fbk_id')->nullable();
            $table->unsignedBigInteger('dep_id')->nullable();
            $table->integer('tck_route')->nullable();
            $table->dateTime('tck_date_created')->nullable();
            $table->unsignedBigInteger('tck_created_by')->nullable();
            $table->dateTime('tck_date_action')->nullable();
            $table->integer('tck_action_by')->nullable();
            $table->dateTime('tck_date_verified')->nullable();
            $table->unsignedBigInteger('tck_verified_by')->nullable();
            $table->mediumText('tck_disapprove_details')->nullable();
            $table->tinyInteger('tck_active')->default(1)->nullable();
            $table->tinyInteger('tck_rate')->nullable();
            $table->dateTime('tck_rate_date')->nullable();
            $table->integer('tck_auto_closed')->default(0)->nullable();
            $table->dateTime('tck_auto_closed_date')->nullable();

            $table->foreign('fbk_id')->references('fbk_id')->on('feedbacks');
            $table->foreign('dep_id')->references('dep_id')->on('departments');
            $table->foreign('tck_created_by')->references('usr_id')->on('users');
            $table->foreign('tck_verified_by')->references('usr_id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
