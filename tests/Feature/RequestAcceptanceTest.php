<?php

namespace Tests\Feature;

use App\Filament\Resources\RequestResource;
use App\Models\Request as BookingRequest;
use App\Models\Employee;
use App\Models\Client;
use App\Models\Visit;
use App\Models\Session;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RequestAcceptanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_accept_booking_request_and_create_visit_and_client(): void
    {
        // Authenticate as a Filament user
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        $this->actingAs($user);

        // 1. Create a specialist/employee
        $employee = Employee::create([
            'name' => 'Dr. Ahmed',
            'phone' => '01000000000',
            'work_days' => ['monday', 'tuesday', 'wednesday'],
        ]);

        $today = now()->toDateString();

        // 2. Create a pending request
        $booking = BookingRequest::create([
            'name' => 'Mohamed Ali',
            'phone' => '01234567890',
            'gender' => 'male',
            'date' => $today, // Today to bypass default filter
            'time' => '17:00',
            'booking_type' => 'وقائية',
            'service_type' => 'مساج',
            'total_price' => 500,
            'deposit' => 200,
            'status' => 'pending',
            'description' => 'المساج [الباقة: جسم كامل مكثف]',
        ]);

        // Run the Filament action via Livewire
        $testable = Livewire::test(\App\Filament\Resources\RequestResource\Pages\ListRequests::class);
        $testable->mountTableAction('Accept', $booking);
        $mountedData = $testable->get('mountedTableActionsData.0');

        $sessions = $mountedData['sessions'] ?? [];
        foreach ($sessions as $uuid => $session) {
            $sessions[$uuid]['employee_id'] = $employee->id;
            $sessions[$uuid]['price'] = $session['price'] - ($session['price'] * 0.10);
        }

        $testable->callTableAction('Accept', $booking, data: [
            'name' => 'Mohamed Ali',
            'phone' => '01234567890',
            'gender' => 'male',
            'visit_date' => $today,
            'visit_hour' => '17:00',
            'visit_complaint' => 'المساج [الباقة: جسم كامل مكثف]',
            'total_price' => 500,
            'deposit' => 200,
            'discount_percentage' => 10,
            'price' => 450,
            'paid' => 450,
            'due_from' => 0,
            'due_to' => 0,
            'sessions' => $sessions,
        ])
        ->assertHasNoTableActionErrors();

        // 5. Verify the client was created/updated
        $client = Client::where('phone', '01234567890')->first();
        $this->assertNotNull($client);
        $this->assertEquals('Mohamed Ali', $client->name);
        $this->assertEquals('male', $client->gender);

        // 6. Verify the visit was created
        $visit = Visit::where('client_id', $client->id)->first();
        $this->assertNotNull($visit);
        $this->assertEquals($today, $visit->date);
        $this->assertEquals('17', $visit->hour);
        $this->assertEquals(450, $visit->price);
        $this->assertEquals(450, $visit->paid);
        $this->assertEquals(10, $visit->discount_percentage);

        // 7. Verify the session was created and assigned to the employee
        $session = Session::where('visit_id', $visit->id)->first();
        $this->assertNotNull($session);
        $this->assertEquals($employee->id, $session->employee_id);
        $this->assertEquals(450, $session->price);
        $this->assertEquals('مساج وقائي (جزئي)', $session->type);

        // 8. Verify request status is now confirmed
        $booking->refresh();
        $this->assertEquals('confirmed', $booking->status);
    }
}
