<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    protected $admin_url;

    /** @test */
    public function home_page()
    {
        $response = $this->get(config('app.admin_url') . '/auth');

        $response->assertStatus(200);
    }
    /** @test */
    // public function login()
    // {
    //     $email = 'admin@code95.com';
    //     $password = 'password';

    //     $response = $this->post('/auth/login', [
    //         'email' => $email,
    //         'password' => $password,
    //     ]);

    //     $response->assertStatus(200);
    // }
    /** @test */
    public function it_returns_200()
    {
        $response = $this->get(config('app.admin_url') . '/users');
        $response->assertStatus(200);
    }

    /** @test */
    public function it_returns_users_collection()
    {
        factory(User::class, 3)->create();

        $response = $this->get(config('app.admin_url') . '/users');

        $response->assertJsonCount(3, 'data');
    }
}
