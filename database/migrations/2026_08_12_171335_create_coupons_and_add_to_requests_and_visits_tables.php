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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('type')->default('fixed'); // 'fixed' or 'percentage'
            $table->decimal('value', 10, 2);
            $table->date('expires_at')->nullable();
            $table->integer('max_uses')->nullable();
            $table->integer('uses')->default(0);
            $table->decimal('min_booking_value', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('requests', function (Blueprint $table) {
            $table->string('coupon_code')->nullable();
            $table->decimal('coupon_discount', 10, 2)->default(0);
        });

        Schema::table('visits', function (Blueprint $table) {
            $table->string('coupon_code')->nullable();
            $table->decimal('coupon_discount', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropColumn(['coupon_code', 'coupon_discount']);
        });

        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn(['coupon_code', 'coupon_discount']);
        });

        Schema::dropIfExists('coupons');
    }
};
