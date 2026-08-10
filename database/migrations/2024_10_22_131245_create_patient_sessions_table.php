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
        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->id();
                $table->integer('price')->nullable();
                $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
                $table->text('notes')->nullable();
                $table->integer('time_or_num')->nullable();
                $table->integer('improvement_percentage')->nullable();
                $table->string('type')->nullable();
                $table->foreignId('visit_id')->constrained('visits')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
