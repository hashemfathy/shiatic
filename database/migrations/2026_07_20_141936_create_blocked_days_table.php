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
        Schema::create('blocked_days', function (Blueprint $table) {
            $table->id();
            $table->string('label')->nullable();
            $table->string('type')->default('specific_date'); // 'specific_date', 'recurring'
            $table->date('specific_date')->nullable();
            $table->unsignedTinyInteger('day_of_week')->nullable(); // 0 (Sunday) to 6 (Saturday)
            $table->string('monthly_week')->nullable(); // 'first', 'second', 'third', 'fourth', 'last', 'any'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocked_days');
    }
};
