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
        Schema::create('massage_protocols', function (Blueprint $table) {
            $table->id();
            $table->string('blood_type'); // 'O', 'A', 'B', 'AB'
            $table->string('pain_level'); // 'severe' (شديد), 'moderate' (متوسط)
            $table->string('weight_bracket'); // '30_55', '55_100', '100_300'
            $table->string('name')->nullable(); // e.g. "فصيلة O - ألم شديد (30-55 كجم)"

            // Luxury (فاخر / مكثف)
            $table->decimal('luxury_price_per_technique', 8, 2)->default(21.00);
            $table->decimal('luxury_duration_minutes', 5, 2)->default(1.50);
            $table->unsignedInteger('luxury_reps')->default(15);

            // Economy (اقتصادي)
            $table->decimal('economy_price_per_technique', 8, 2)->default(14.00);
            $table->decimal('economy_duration_minutes', 5, 2)->default(1.00);
            $table->unsignedInteger('economy_reps')->default(10);

            // Default Intensity & Speed
            $table->unsignedTinyInteger('intensity_percent')->default(30); // 30%, 40%, 50%, 60%
            $table->unsignedTinyInteger('speed_percent')->default(20); // 20%
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['blood_type', 'pain_level', 'weight_bracket'], 'msg_proto_idx');
        });

        Schema::create('massage_techniques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('massage_protocol_id')->constrained('massage_protocols')->cascadeOnDelete();
            $table->unsignedInteger('order')->default(1); // عدد / م (1 to 88 or 1 to 60)
            $table->unsignedTinyInteger('region_number'); // ر م (1 to 39)
            $table->string('region_name'); // المنطقة (مثل الخلفيه الخارجيه)
            $table->string('massage_type'); // نوع المساج (مثل وتري زلالي)
            $table->string('tool')->nullable(); // الاداه (كلوه, ابهام, قبضه, كفين...)
            $table->string('direction')->nullable(); // الاتجاه (اعلى لاسفل, اسفل لاعلى...)
            $table->string('reps_display')->nullable(); // العدد لكل وزن (15/10, 20/15, 25/20)
            $table->unsignedTinyInteger('intensity_percent')->nullable(); // الشده
            $table->unsignedTinyInteger('speed_percent')->nullable(); // السرعه
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['massage_protocol_id', 'order']);
            $table->index(['region_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('massage_techniques');
        Schema::dropIfExists('massage_protocols');
    }
};
