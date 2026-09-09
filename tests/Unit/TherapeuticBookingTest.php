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
        $this->assertEquals(37, $chiroData['total_techniques']); // Group 2 (17) + Group 5 (20) = 37

        // Build a mock therapeutic request
        $descParts = [
            "نوع الجلسة: سيشن علاجية [البروتوكول: مكثف]",
            "بيانات المريض: فصيلة الدم (A) | الوزن (75 كجم)",
            "مناطق الألم: شديد [17] | متوسط [1]",
            "المساج العلاجي [التكنيك: مسحي علاجي | عدد التكنيكات: 4 | السعر: 128.00 ج.م | المدة: 8.0 دقيقة]",
            "الكيروبراكتيك العلاجي [المناطق: منطقة 2 + منطقة 5 | عدد التكنيكات: 37 | السعر: 600.51 ج.م | المدة: 9.3 دقيقة]",
            "الحجامة [عدد الكاسات: 1 كاس على مناطق الألم الشديد | السعر: 45.00 ج.م | المدة: 11 دقيقة]",
            "التأهيل [برنامج تمارين تأهيلية | السعر: 120.00 ج.م | المدة: 10 دقيقة]",
        ];

        $record = (object)[
            'booking_type' => 'علاجية',
            'service_type' => 'مساج علاجي + كيروبراكتيك علاجي + حجامة + تأهيل',
            'total_price' => 893.51,
            'description' => implode(' | ', $descParts),
        ];

        // Test base prices parsing
        $basePrices = MassageHelper::calculateServiceBasePrices($record);
        $this->assertEquals(128.00, $basePrices['massage']);
        $this->assertEquals(600.51, $basePrices['cracking']);
        $this->assertEquals(45.00, $basePrices['hijama']);
        $this->assertEquals(120.00, $basePrices['rehab']);

        // Test HTML rendering
        $html = TherapeuticMassageHelper::renderTherapeuticDetails($record);
        $this->assertStringContainsString('تفاصيل السيشن العلاجية', (string)$html);
        $this->assertStringContainsString('المساج العلاجي', (string)$html);
        $this->assertStringContainsString('الكيروبراكتيك العلاجي', (string)$html);
    }
}
