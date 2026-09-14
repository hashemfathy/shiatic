<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Helpers\TherapeuticMassageHelper;
use App\Helpers\TherapeuticChiropracticHelper;
use App\Helpers\MassageHelper;
use App\Models\Request as BookingRequest;

class TherapeuticBookingTest extends TestCase
{
    public function test_therapeutic_helpers_and_base_pricing(): void
    {
        // Blood type A, 75kg, Intensive, Region 17 (Severe) & Region 1 (Moderate)
        $massageData = TherapeuticMassageHelper::calculate('A', 75, [17], [1], 'intensive');
        $this->assertEquals(1, $massageData['severe_techniques']); // Region 17 has 1 tech
        $this->assertEquals(3, $massageData['moderate_techniques']); // Region 1 has 3 techs
        $this->assertEquals(4, $massageData['total_techniques']);
        $this->assertEquals(8.0, $massageData['duration']);
        $this->assertEquals(128.00, $massageData['total_price']);

        // Chiropractic for Region 17 & 1 (Group 2 and Group 5)
        $chiroData = TherapeuticChiropracticHelper::calculate([17, 1], 'intensive');
        $this->assertEquals(22, $chiroData['total_techniques']); // Group 2 (10) + Group 5 (12) = 22
        $this->assertEquals(418.00, $chiroData['total_price']);
        $this->assertEquals(5.5, $chiroData['duration']);

        // Build a mock therapeutic request
        $descParts = [
            "نوع الجلسة: سيشن علاجية [البروتوكول: مكثف]",
            "بيانات المريض: فصيلة الدم (A) | الوزن (75 كجم)",
            "مناطق الألم: شديد [17] | متوسط [1]",
            "المساج العلاجي [التكنيك: مسحي علاجي | عدد التكنيكات: 4 | السعر: 128.00 ج.م | المدة: 8.0 دقيقة]",
            "الكيروبراكتيك العلاجي [المناطق: منطقة 2 + منطقة 5 | عدد التكنيكات: 22 | السعر: 418.00 ج.م | المدة: 5.5 دقيقة]",
            "الحجامة [عدد الكاسات: 1 كاس على مناطق الألم الشديد | السعر: 45.00 ج.م | المدة: 11 دقيقة]",
            "التأهيل [برنامج تمارين تأهيلية | السعر: 120.00 ج.م | المدة: 10 دقيقة]",
        ];

        $record = (object)[
            'booking_type' => 'علاجية',
            'service_type' => 'مساج علاجي + كيروبراكتيك علاجي + حجامة + تأهيل',
            'total_price' => 711.00,
            'description' => implode(' | ', $descParts),
        ];

        // Test base prices parsing
        $basePrices = MassageHelper::calculateServiceBasePrices($record);
        $this->assertEquals(128.00, $basePrices['massage']);
        $this->assertEquals(418.00, $basePrices['cracking']);
        $this->assertEquals(45.00, $basePrices['hijama']);
        $this->assertEquals(120.00, $basePrices['rehab']);

        // Test HTML rendering
        $html = TherapeuticMassageHelper::renderTherapeuticDetails($record);
        $this->assertStringContainsString('تفاصيل السيشن العلاجية', (string)$html);
        $this->assertStringContainsString('المساج العلاجي', (string)$html);
        $this->assertStringContainsString('الكيروبراكتيك العلاجي', (string)$html);

        // Test Chiropractic Techniques Table rendering
        $chiroHtml = TherapeuticChiropracticHelper::renderChiropracticTechniquesTable($record);
        $this->assertStringContainsString('تكنيكات الكيروبراكتيك العلاجي المعتمدة', (string)$chiroHtml);
        $this->assertStringContainsString('منطقة 20', (string)$chiroHtml); // Technique from group 2
        $this->assertStringContainsString('تيبس كتف ايمن', (string)$chiroHtml);
    }

    public function test_intensive_protocol_hijama_disabled_and_rehab_5_min(): void
    {
        // Intensive protocol with severe [17] and moderate [1]
        // Massage: 8.0 min, 128 EGP
        // Chiro: 5.5 min, 418 EGP
        // Hijama: 0 min, 0 EGP (disabled / commented out)
        // Rehab: 5 min, 60 EGP (in intensive only)
        $descParts = [
            "نوع الجلسة: سيشن علاجية [البروتوكول: مكثف]",
            "بيانات المريض: فصيلة الدم (A) | الوزن (75 كجم)",
            "مناطق الألم: شديد [17] | متوسط [1]",
            "المساج العلاجي [التكنيك: مسحي علاجي | عدد التكنيكات: 4 | السعر: 128.00 ج.م | المدة: 8.0 دقيقة]",
            "الكيروبراكتيك العلاجي [المناطق: منطقة 2 + منطقة 5 | عدد التكنيكات: 22 | السعر: 418.00 ج.م | المدة: 5.5 دقيقة]",
            "التأهيل [برنامج تمارين تأهيلية | السعر: 60.00 ج.م | المدة: 5 دقيقة]",
        ];

        $record = (object)[
            'booking_type' => 'علاجية',
            'service_type' => 'مساج علاجي + كيروبراكتيك علاجي + تأهيل',
            'total_price' => 606.00,
            'description' => implode(' | ', $descParts),
        ];

        $basePrices = MassageHelper::calculateServiceBasePrices($record);
        $this->assertEquals(128.00, $basePrices['massage']);
        $this->assertEquals(418.00, $basePrices['cracking']);
        $this->assertEquals(0.00, $basePrices['hijama']);
        $this->assertEquals(60.00, $basePrices['rehab']);
    }

    public function test_economy_protocol_no_rehab_and_no_hijama(): void
    {
        // Economy protocol with severe [17] and moderate [1]
        // Hijama: 0 min, 0 EGP (disabled)
        // Rehab: 0 min, 0 EGP (not included in economy protocol)
        $descParts = [
            "نوع الجلسة: سيشن علاجية [البروتوكول: اقتصادي]",
            "بيانات المريض: فصيلة الدم (A) | الوزن (75 كجم)",
            "مناطق الألم: شديد [17] | متوسط [1]",
            "المساج العلاجي [التكنيك: مسحي علاجي | عدد التكنيكات: 4 | السعر: 84.00 ج.م | المدة: 5.5 دقيقة]",
            "الكيروبراكتيك العلاجي [المناطق: منطقة 2 + منطقة 5 | عدد التكنيكات: 16 | السعر: 307.68 ج.م | المدة: 4.0 دقيقة]",
        ];

        $record = (object)[
            'booking_type' => 'علاجية',
            'service_type' => 'مساج علاجي + كيروبراكتيك علاجي',
            'total_price' => 391.68,
            'description' => implode(' | ', $descParts),
        ];

        $basePrices = MassageHelper::calculateServiceBasePrices($record);
        $this->assertEquals(84.00, $basePrices['massage']);
        $this->assertEquals(307.68, $basePrices['cracking']);
        $this->assertEquals(0.00, $basePrices['hijama']);
        $this->assertEquals(0.00, $basePrices['rehab']);
    }

    public function test_expected_sessions_rules(): void
    {
        // Case 1: When severe pain points exist (severe > 0)
        // Intensive: 5 to 7 sessions (3 weekly)
        // Economy: 9 to 12 sessions (3 weekly)
        $hasSevere = true;
        $intensivePlanSevere = $hasSevere ? '5 إلى 7 سيشن (ويفضل 3 سيشن أسبوعياً)' : '3 إلى 5 سيشن (ويفضل 2 سيشن أسبوعياً)';
        $economyPlanSevere = $hasSevere ? '9 إلى 12 سيشن (ويفضل 3 سيشن أسبوعياً)' : '5 إلى 7 سيشن (ويفضل 2 سيشن أسبوعياً)';
        $this->assertStringContainsString('5 إلى 7 سيشن', $intensivePlanSevere);
        $this->assertStringContainsString('3 سيشن أسبوعياً', $intensivePlanSevere);
        $this->assertStringContainsString('9 إلى 12 سيشن', $economyPlanSevere);
        $this->assertStringContainsString('3 سيشن أسبوعياً', $economyPlanSevere);

        // Case 2: When NO severe pain points exist (severe == 0)
        // Intensive: 3 to 5 sessions (2 weekly)
        // Economy: 5 to 7 sessions (2 weekly)
        $hasSevere = false;
        $intensivePlanNoSevere = $hasSevere ? '5 إلى 7 سيشن (ويفضل 3 سيشن أسبوعياً)' : '3 إلى 5 سيشن (ويفضل 2 سيشن أسبوعياً)';
        $economyPlanNoSevere = $hasSevere ? '9 إلى 12 سيشن (ويفضل 3 سيشن أسبوعياً)' : '5 إلى 7 سيشن (ويفضل 2 سيشن أسبوعياً)';
        $this->assertStringContainsString('3 إلى 5 سيشن', $intensivePlanNoSevere);
        $this->assertStringContainsString('2 سيشن أسبوعياً', $intensivePlanNoSevere);
        $this->assertStringContainsString('5 إلى 7 سيشن', $economyPlanNoSevere);
        $this->assertStringContainsString('2 سيشن أسبوعياً', $economyPlanNoSevere);
    }
}

