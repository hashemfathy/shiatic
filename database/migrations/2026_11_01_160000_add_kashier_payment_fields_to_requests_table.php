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
            if (!Schema::hasColumn('requests', 'payment_status')) {
                $table->string('payment_status')->default('unpaid')->after('status');
            }
            if (!Schema::hasColumn('requests', 'transaction_id')) {
                $table->string('transaction_id')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('requests', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('transaction_id');
            }
            if (!Schema::hasColumn('requests', 'paid_amount')) {
                $table->decimal('paid_amount', 10, 2)->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('requests', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('paid_amount');
            }
            if (!Schema::hasColumn('requests', 'payment_meta')) {
                $table->text('payment_meta')->nullable()->after('paid_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['payment_status', 'transaction_id', 'payment_method', 'paid_amount', 'paid_at', 'payment_meta'] as $col) {
                if (Schema::hasColumn('requests', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
