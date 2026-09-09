<?php

namespace Tests\Unit;

use App\Helpers\TherapeuticMassageHelper;
use PHPUnit\Framework\TestCase;

class TherapeuticMassageTest extends TestCase
{
    public function test_calculate_with_severe_region_17(): void
    {
        // In the updated docx files, region 17 has 1 technique
        $result = TherapeuticMassageHelper::calculate(
            'A',
            75.0,
            [17], // region 17 severe
            [],
            'intensive'
        );

        $this->assertEquals('55_100', $result['weight_bracket']);
        $this->assertEquals(1, $result['severe_count']);
        $this->assertEquals(1, $result['severe_techniques']);
        // 55_100 intensive: 1 technique * 2.0 min = 2.0 min, 1 technique * 32 EGP = 32 EGP
        $this->assertEquals(2.0, $result['duration']);
        $this->assertEquals(32.0, $result['total_price']);
    }

    public function test_calculate_with_severe_region_11(): void
    {
        // Region 11 has 6 techniques in severe pain
        $result = TherapeuticMassageHelper::calculate(
            'A',
            75.0,
            [11], // region 11 severe
            [],
            'intensive'
        );

        $this->assertEquals(6, $result['severe_techniques']);
        // 6 techniques * 2.0 min = 12.0 min
        $this->assertEquals(12.0, $result['duration']);
        // 6 techniques * 32 EGP = 192 EGP
        $this->assertEquals(192.0, $result['total_price']);
    }

    public function test_calculate_with_no_pain_regions(): void
    {
        $result = TherapeuticMassageHelper::calculate('B', 60.0, [], [], 'economy');

        $this->assertEquals(0, $result['duration']);
        $this->assertEquals(0, $result['total_price']);
    }
}
