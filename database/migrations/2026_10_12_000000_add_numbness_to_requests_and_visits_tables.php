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
        Schema::table('requests', function (Blueprint $table) {
            if (!Schema::hasColumn('requests', 'numbness')) {
                $table->string('numbness')->nullable()->after('gender');
            }
        });

        Schema::table('visits', function (Blueprint $table) {
            if (!Schema::hasColumn('visits', 'numbness')) {
                $table->string('numbness')->nullable()->after('type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            if (Schema::hasColumn('requests', 'numbness')) {
                $table->dropColumn('numbness');
            }
        });

        Schema::table('visits', function (Blueprint $table) {
            if (Schema::hasColumn('visits', 'numbness')) {
                $table->dropColumn('numbness');
            }
        });
    }
};
