<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_responses', function (Blueprint $table) {
            $table->id('res_id');
            $table->uuid('res_uuid')->unique();
            
            $table->unsignedBigInteger('tck_id');
            
            $table->text('res_message');
            
            $table->dateTime('res_date_created');

            // Foreign key constraint
            $table->foreign('tck_id')->references('tck_id')->on('tickets')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_responses');
    }
};