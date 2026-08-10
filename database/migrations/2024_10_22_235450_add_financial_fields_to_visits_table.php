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
        Schema::table('visits', function (Blueprint $table) {
            if (!Schema::hasColumn('visits', 'paid')) {
                $table->integer('paid')->nullable();
            }
            if (!Schema::hasColumn('visits', 'due_to')) {
                $table->integer('due_to')->nullable();
            }
            if (!Schema::hasColumn('visits', 'due_from')) {
                $table->integer('due_from')->nullable();
            }
            if (!Schema::hasColumn('visits', 'discount_percentage')) {
                $table->integer('discount_percentage')->nullable();
            }
            if (!Schema::hasColumn('visits', 'improvement_percentage')) {
                $table->integer('improvement_percentage')->nullable();
            }
            if (!Schema::hasColumn('visits', 'type')) {
                $table->string('type')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropColumn([
                'paid',
                'due_to',
                'due_from',
                'discount_percentage',
                'improvement_percentage',
                'type'
            ]);
        });
    }
};
