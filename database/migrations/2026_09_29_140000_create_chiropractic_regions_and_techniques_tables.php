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
        Schema::create('chiropractic_regions', function (Blueprint $table) {
            $table->id();
            $table->string('plan_type')->default('intensive'); // 'intensive' (مكثف) or 'economy' (اقتصادي)
            $table->unsignedTinyInteger('region_number'); // 1 to 5
            $table->string('name');
            $table->json('diagram_numbers')->nullable(); // e.g. [15, 16, 37]
            $table->decimal('price_per_technique', 8, 2)->default(13.00);
            $table->unsignedInteger('duration_seconds')->default(15);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['plan_type', 'region_number']);
        });

        Schema::create('chiropractic_techniques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chiropractic_region_id')->constrained('chiropractic_regions')->cascadeOnDelete();
            $table->unsignedInteger('order')->default(1);
            $table->string('target_region_code')->nullable(); // e.g. 16, 37, 20/21/22, 13/14
            $table->string('name'); // e.g. الاذن اليمنى
            $table->string('position')->nullable(); // e.g. الجلوس, النوم على الظهر
            $table->string('direction')->nullable(); // e.g. للخارج, كتف مرتفع, فصل
            $table->unsignedInteger('rep')->default(2); // مللي / عدد التكرارات
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['chiropractic_region_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chiropractic_techniques');
        Schema::dropIfExists('chiropractic_regions');
    }
};
