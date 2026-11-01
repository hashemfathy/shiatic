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
        Schema::table('specialist_days_off', function (Blueprint $table) {
            $table->string('type')->default('specific_date')->after('employee_id');
            $table->date('date')->nullable()->change();
            $table->unsignedTinyInteger('day_of_week')->nullable()->after('date');
            $table->string('monthly_week')->nullable()->default('any')->after('day_of_week');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('specialist_days_off', function (Blueprint $table) {
            $table->dropColumn(['type', 'day_of_week', 'monthly_week']);
            $table->date('date')->nullable(false)->change();
        });
    }
};
