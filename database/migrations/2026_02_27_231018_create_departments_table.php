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
        Schema::create('departments', function (Blueprint $table) {
            $table->id('dep_id');
            $table->string('dep_name')->nullable();
            $table->string('dep_full_name')->nullable();
            $table->string('branch_id')->nullable();
            $table->tinyInteger('dep_active')->nullable();

            $table->foreign('branch_id')
                ->references('branch_id')
                ->on('branches')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
