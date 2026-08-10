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
            $table->string('booking_type')->nullable();
            $table->string('service_type')->nullable();
            $table->json('packages')->nullable();
            $table->decimal('total_price', 10, 2)->nullable();
            $table->integer('total_duration')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn(['booking_type', 'service_type', 'packages', 'total_price', 'total_duration']);
        });
    }
};
