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
        Schema::create('ticket_returns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tck_id');
            $table->unsignedBigInteger('fbk_id');
            $table->unsignedBigInteger('returned_by_usr_id'); // Who rejected it
            $table->string('routing_source'); // 'AI' or 'Admin'
            $table->text('return_reason');
            $table->timestamp('returned_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_returns');
    }
};
