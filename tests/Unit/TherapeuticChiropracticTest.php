<?php

namespace Tests\Unit;

use App\Helpers\TherapeuticChiropracticHelper;
use PHPUnit\Framework\TestCase;

class TherapeuticChiropracticTest extends TestCase
{
    public function test_calculate_group_1_cervical_intensive(): void
    {
        // Region 37 belongs to Group 1 (العنقية)
        // Group 1 intensive techniques count = 13
        $result = TherapeuticChiropracticHelper::calculate([37], 'intensive');

        $this->assertEquals([1], $result['active_groups']);
        $this->assertEquals(13, $result['total_techniques']);
        // 13 * 0.25 min = 3.25 min
        $this->assertEquals(3.25, $result['duration']);
        // 13 * 16.23 EGP = 210.99 EGP
        $this->assertEquals(210.99, $result['total_price']);
    }

    public function test_calculate_group_3_thoracic_intensive(): void
    {
        // Region 13 belongs to Group 3 (الصدرية)
        // Group 3 intensive techniques count = 13
        $result = TherapeuticChiropracticHelper::calculate([13], 'intensive');

        $this->assertEquals([3], $result['active_groups']);
        $this->assertEquals(13, $result['total_techniques']);
        // 13 * 0.25 min = 3.25 min
        $this->assertEquals(3.25, $result['duration']);
        // 13 * 16.23 EGP = 210.99 EGP
        $this->assertEquals(210.99, $result['total_price']);
    }

    public function test_calculate_single_region_group(): void
    {
        // Region 17 belongs to Group 2 (الأكتاف والذراعين)
        // Group 2 intensive techniques count = 17
        $result = TherapeuticChiropracticHelper::calculate([17], 'intensive');

        $this->assertEquals([2], $result['active_groups']);
        $this->assertEquals(17, $result['total_techniques']);
        // 17 * 0.25 min = 4.25 min
        $this->assertEquals(4.25, $result['duration']);
        // 17 * 16.23 EGP = 275.91 EGP
        $this->assertEquals(275.91, $result['total_price']);
    }

    public function test_calculate_region_17_and_region_1(): void
    {
        // Region 17 belongs to Group 2 (17 techniques in intensive)
        // Region 1 belongs to Group 5 (20 techniques in intensive)
        // Total techniques = 17 + 20 = 37 techniques
        $result = TherapeuticChiropracticHelper::calculate([17, 1], 'intensive');

        $this->assertEquals([2, 5], $result['active_groups']);
        $this->assertEquals(37, $result['total_techniques']);
        // 37 * 0.25 min = 9.25 min
        $this->assertEquals(9.25, $result['duration']);
        // 37 * 16.23 EGP = 600.51 EGP
        $this->assertEquals(600.51, $result['total_price']);
    }

    public function test_calculate_multiple_regions_same_group(): void
    {
        // Regions 17 and 18 both belong to Group 2 (الأكتاف والذراعين)
        // Selecting multiple regions in the same group activates Group 2 ONCE (17 techniques)
        $result = TherapeuticChiropracticHelper::calculate([17, 18], 'intensive');

        $this->assertEquals([2], $result['active_groups']);
        $this->assertEquals(17, $result['total_techniques']);
        $this->assertEquals(4.25, $result['duration']);
        $this->assertEquals(275.91, $result['total_price']);
    }

    public function test_calculate_economy_style(): void
    {
        // Region 17 belongs to Group 2 (13 techniques in economy)
        // Region 12 belongs to Group 4 (10 techniques in economy)
        // Total = 13 + 10 = 23 techniques
        $result = TherapeuticChiropracticHelper::calculate([17, 12], 'economy');

        $this->assertEquals([2, 4], $result['active_groups']);
        $this->assertEquals(23, $result['total_techniques']);
        // 23 * 0.25 min = 5.75 min
        $this->assertEquals(5.75, $result['duration']);
        // 23 * 16.23 EGP = 373.29 EGP
        $this->assertEquals(373.29, $result['total_price']);
    }
}
