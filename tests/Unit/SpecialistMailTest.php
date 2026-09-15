<?php

namespace Tests\Unit;

use App\Mail\SpecialistAssignedMail;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Visit;
use Tests\TestCase;

class SpecialistMailTest extends TestCase
{
    public function test_specialist_assigned_mail_renders_correctly()
    {
        $employee = new Employee([
            'name' => 'د. حسام الشريف',
            'email' => 'specialist@example.com',
            'phone' => '01011112222',
        ]);

        $client = new Client([
            'name' => 'محمد أحمد',
            'phone' => '01099998888',
            'gender' => 'male',
        ]);

        $visit = new Visit([
            'date' => '2026-09-20',
            'hour' => 14.5, // 2:30 PM
            'type' => 'علاجية',
            'complaint' => 'ألم شديد أسفل الظهر مع تنميل في القدم',
        ]);
        $visit->setRelation('client', $client);

        $sessions = [
            ['type' => 'مساج علاجي', 'price' => 750],
            ['type' => 'كيروبراكتيك علاجي', 'price' => 600],
        ];

        $mailable = new SpecialistAssignedMail($employee, $visit, $sessions);

        $mailable->assertSeeInOrderInHtml([
            'دكتور / د. حسام الشريف',
            'محمد أحمد',
            '2026-09-20',
            'مساج علاجي',
            'كيروبراكتيك علاجي',
            'ألم شديد أسفل الظهر مع تنميل في القدم',
        ]);
    }

    public function test_new_request_mail_renders_correctly()
    {
        $request = new \App\Models\Request([
            'name' => 'أحمد محمود',
            'phone' => '01012345678',
            'gender' => 'male',
            'booking_type' => 'علاجية',
            'service_type' => 'مساج علاجي',
            'total_price' => 1500,
            'deposit' => 600,
            'date' => '2026-09-25',
            'time' => '16:00',
        ]);

        $mailable = new \App\Mail\NewRequestMail($request);

        $mailable->assertSeeInOrderInHtml([
            'طلب حجز جديد',
            'أحمد محمود',
            '01012345678',
            'علاجية',
            'مساج علاجي',
            '1500',
        ]);
    }
}
