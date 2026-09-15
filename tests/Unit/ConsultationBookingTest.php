<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Request as BookingRequest;
use App\Filament\Resources\RequestResource;

class ConsultationBookingTest extends TestCase
{
    public function test_consultation_totals_calculation_in_filament_resource(): void
    {
        $state = [
            'booking_type' => 'موعد مع مختص',
            'consultation_age' => 35,
            'consultation_weight' => 80,
            'consultation_blood_type' => 'B',
            'consultation_notes' => 'آلام في أسفل الظهر منذ أسبوعين',
            'is_urgent' => false,
            'coupon_code' => null,
            'coupon_discount' => 0,
        ];

        $getMock = function ($key) use ($state) {
            return $state[$key] ?? null;
        };

        $results = [];
        $setMock = function ($key, $value) use (&$results) {
            $results[$key] = $value;
        };

        RequestResource::updateConsultationTotals($setMock, $getMock);

        $this->assertEquals(200.0, $results['total_price']);
        $this->assertEquals(15, $results['total_duration']);
        $this->assertEquals(200, $results['deposit']); // 100% deposit
        $this->assertEquals('موعد مع مختص', $results['service_type']);
        $this->assertEquals(['consultation'], $results['packages']);
        $this->assertStringContainsString('موعد مع مختص [استشارة]', $results['description']);
        $this->assertStringContainsString('السن (35)', $results['description']);
        $this->assertStringContainsString('فصيلة الدم (B)', $results['description']);
        $this->assertStringContainsString('الوزن (80 كجم)', $results['description']);
        $this->assertStringContainsString('آلام في أسفل الظهر منذ أسبوعين', $results['description']);
        $this->assertStringContainsString('15 دقيقة', $results['description']);
        $this->assertStringContainsString('200.00 ج.م', $results['description']);
    }

    public function test_consultation_totals_with_urgent_booking_and_coupon(): void
    {
        $state = [
            'booking_type' => 'موعد مع مختص',
            'consultation_age' => 28,
            'consultation_weight' => 70,
            'consultation_blood_type' => 'O',
            'consultation_notes' => 'استشارة عامة',
            'is_urgent' => true,
            'coupon_code' => 'DISC50',
            'coupon_discount' => 50,
        ];

        $getMock = function ($key) use ($state) {
            return $state[$key] ?? null;
        };

        $results = [];
        $setMock = function ($key, $value) use (&$results) {
            $results[$key] = $value;
        };

        RequestResource::updateConsultationTotals($setMock, $getMock);

        // Price = 200 + 200 (urgent fee) - 50 (coupon) = 350
        $this->assertEquals(350.0, $results['total_price']);
        $this->assertEquals(15, $results['total_duration']);
        $this->assertEquals(350, $results['deposit']); // 100% deposit of final price
        $this->assertStringContainsString('الحجز المستعجل', $results['description']);
        $this->assertStringContainsString('كوبون الخصم [الكود: DISC50 | الخصم: 50 ج.م]', $results['description']);
    }

    public function test_consultation_edit_form_mutation(): void
    {
        $desc = "نوع الجلسة: موعد مع مختص [استشارة] | بيانات المريض: السن (42) | فصيلة الدم (AB) | الوزن (85 كجم) | الشكوى أو سبب الاستشارة: انزلاق غضروفي قطني | مدة الموعد: 15 دقيقة | السعر: 200.00 ج.م";

        $data = [
            'booking_type' => 'موعد مع مختص',
            'description' => $desc,
        ];

        // Simulate fill mutation
        $age = 30;
        if (preg_match('/السن \((\d+)\)/u', $desc, $m)) $age = (int)$m[1];
        $weight = 70;
        if (preg_match('/الوزن \((\d+(\.\d+)?)(?:\s*كجم)?\)/u', $desc, $m)) $weight = (float)$m[1];
        $bloodType = 'O';
        if (preg_match('/فصيلة الدم \((A|B|AB|O)\)/ui', $desc, $m)) $bloodType = strtoupper($m[1]);
        $notes = '';
        if (preg_match('/الشكوى أو سبب الاستشارة: ([^|]+)/u', $desc, $m)) $notes = trim($m[1]);

        $this->assertEquals(42, $age);
        $this->assertEquals(85.0, $weight);
        $this->assertEquals('AB', $bloodType);
        $this->assertEquals('انزلاق غضروفي قطني', $notes);
    }

    public function test_consultation_base_prices_calculation(): void
    {
        $record = (object)[
            'booking_type' => 'موعد مع مختص',
            'service_type' => 'موعد مع مختص',
            'total_price' => 200.0,
        ];

        $prices = \App\Helpers\MassageHelper::calculateServiceBasePrices($record);

        $this->assertEquals(200.0, $prices['consultation']);
        $this->assertEquals(0, $prices['massage']);
        $this->assertEquals(0, $prices['cracking']);
    }
}
