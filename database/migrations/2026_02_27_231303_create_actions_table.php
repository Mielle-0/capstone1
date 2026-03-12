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
        Schema::create('actions', function (Blueprint $table) {
            $table->id('act_id');
            $table->string('act_uuid')->nullable();
            $table->unsignedBigInteger('tck_id')->nullable();
            $table->mediumText('act_details')->nullable();
            $table->dateTime('act_date_created')->nullable();
            $table->unsignedBigInteger('act_created_by')->nullable();
            $table->tinyInteger('act_status')->default(0)->nullable();
            $table->mediumText('act_reject_details')->nullable();
            $table->dateTime('act_date_verified')->nullable();
            $table->unsignedBigInteger('act_verified_by')->nullable();
            $table->tinyInteger('act_active')->default(1)->nullable();
            $table->string('act_file')->nullable();
            $table->tinyInteger('act_auto_closed')->default(0)->nullable();
            $table->dateTime('act_auto_closed_date')->nullable();

            $table->foreign('tck_id')->references('tck_id')->on('tickets');
            $table->foreign('act_created_by')->references('usr_id')->on('users');
            $table->foreign('act_verified_by')->references('usr_id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actions');
    }
};
