<?php

namespace Tests\Unit;

use App\Helpers\TherapeuticChiropracticHelper;
use PHPUnit\Framework\TestCase;

class TherapeuticChiropracticTest extends TestCase
{
    public function test_calculate_group_1_cervical_intensive(): void
    {
        // Region 37 belongs to Group 1 (العنقية)
        // Group 1 intensive techniques count = 10 (20 EGP/tech)
        $result = TherapeuticChiropracticHelper::calculate([37], 'intensive');

        $this->assertEquals([1], $result['active_groups']);
        $this->assertEquals(10, $result['total_techniques']);
        // 10 * 0.25 min = 2.5 min
        $this->assertEquals(2.5, $result['duration']);
        // 10 * 20.00 EGP = 200.00 EGP
        $this->assertEquals(200.00, $result['total_price']);
    }

    public function test_calculate_group_3_thoracic_intensive(): void
    {
        // Region 13 belongs to Group 3 (الصدرية)
        // Group 3 intensive techniques count = 10 (12 EGP/tech)
        $result = TherapeuticChiropracticHelper::calculate([13], 'intensive');

        $this->assertEquals([3], $result['active_groups']);
        $this->assertEquals(10, $result['total_techniques']);
        // 10 * 0.25 min = 2.5 min
        $this->assertEquals(2.5, $result['duration']);
        // 10 * 12.00 EGP = 120.00 EGP
        $this->assertEquals(120.00, $result['total_price']);
    }

    public function test_calculate_single_region_group(): void
    {
        // Region 17 belongs to Group 2 (الأكتاف والذراعين)
        // Group 2 intensive techniques count = 10 (7 EGP/tech)
        $result = TherapeuticChiropracticHelper::calculate([17], 'intensive');

        $this->assertEquals([2], $result['active_groups']);
        $this->assertEquals(10, $result['total_techniques']);
        // 10 * 0.25 min = 2.5 min
        $this->assertEquals(2.5, $result['duration']);
        // 10 * 7.00 EGP = 70.00 EGP
        $this->assertEquals(70.00, $result['total_price']);
    }

    public function test_calculate_region_17_and_region_1(): void
    {
        // Region 17 belongs to Group 2 (10 techniques @ 7 = 70 EGP)
        // Region 1 belongs to Group 5 (12 techniques @ 10 = 120 EGP)
        // Total techniques = 10 + 12 = 22 techniques, Total price = 70 + 120 = 190 EGP
        $result = TherapeuticChiropracticHelper::calculate([17, 1], 'intensive');

        $this->assertEquals([2, 5], $result['active_groups']);
        $this->assertEquals(22, $result['total_techniques']);
        // 22 * 0.25 min = 5.5 min
        $this->assertEquals(5.5, $result['duration']);
        $this->assertEquals(190.00, $result['total_price']);
    }

    public function test_calculate_multiple_regions_same_group(): void
    {
        // Regions 17 and 18 both belong to Group 2 (الأكتاف والذراعين)
        // Selecting multiple regions in the same group activates Group 2 ONCE (10 techniques @ 7 = 70 EGP)
        $result = TherapeuticChiropracticHelper::calculate([17, 18], 'intensive');

        $this->assertEquals([2], $result['active_groups']);
        $this->assertEquals(10, $result['total_techniques']);
        $this->assertEquals(2.5, $result['duration']);
        $this->assertEquals(70.00, $result['total_price']);
    }

    public function test_group_2_includes_region_34_and_36(): void
    {
        // Regions 34 and 36 are listed under Group 2 (الأكتاف والذراعين)
        $this->assertEquals(2, TherapeuticChiropracticHelper::getGroupForRegion(34));
        $this->assertEquals(2, TherapeuticChiropracticHelper::getGroupForRegion(36));
    }

    public function test_calculate_economy_style(): void
    {
        // Region 17 belongs to Group 2 (8 techniques @ 7 = 56 EGP)
        // Region 12 belongs to Group 4 (8 techniques @ 20 = 160 EGP)
        // Total = 8 + 8 = 16 techniques, Total price = 56 + 160 = 216 EGP
        $result = TherapeuticChiropracticHelper::calculate([17, 12], 'economy');

        $this->assertEquals([2, 4], $result['active_groups']);
        $this->assertEquals(16, $result['total_techniques']);
        // 16 * 0.25 min = 4.0 min
        $this->assertEquals(4.0, $result['duration']);
        $this->assertEquals(216.00, $result['total_price']);
    }

    public function test_whole_body_counts(): void
    {
        // Intensive: 10 + 10 + 10 + 11 + 12 = 53
        // Raw price: 200 + 70 + 120 + 220 + 120 = 730 EGP.
        // Discount 15% (5 groups > 3): 730 * 0.85 = 620.50 EGP
        $intResult = TherapeuticChiropracticHelper::calculate([15, 17, 13, 9, 1], 'intensive');
        $this->assertEquals(53, $intResult['total_techniques']);
        $this->assertEquals(13.25, $intResult['duration']);
        $this->assertEquals(620.50, $intResult['total_price']);

        // Economy: 8 * 5 = 40
        // Raw price: 144 + 56 + 96 + 160 + 80 = 536 EGP.
        // Discount 15% (5 groups > 3): 536 * 0.85 = 455.60 EGP
        $ecoResult = TherapeuticChiropracticHelper::calculate([15, 17, 13, 9, 1], 'economy');
        $this->assertEquals(40, $ecoResult['total_techniques']);
        $this->assertEquals(10.0, $ecoResult['duration']);
        $this->assertEquals(455.60, $ecoResult['total_price']);
    }

    public function test_techniques_catalog_populated(): void
    {
        $intensiveTechs = TherapeuticChiropracticHelper::getTechniquesForGroups([1, 2], 'intensive');
        $this->assertCount(20, $intensiveTechs); // 10 + 10

        $economyTechs = TherapeuticChiropracticHelper::getTechniquesForGroups([1, 2], 'economy');
        $this->assertCount(16, $economyTechs); // 8 + 8
    }
}
