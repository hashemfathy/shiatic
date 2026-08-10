<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed default values
        \Illuminate\Support\Facades\DB::table('settings')->insert([
            ['key' => 'urgent_booking_fee', 'value' => '200', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'max_female_bookings', 'value' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'max_male_bookings', 'value' => '3', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
