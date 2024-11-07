<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserLoginTest extends TestCase
{
    public function test_login_with_correct_data(): void
    {
        $response = $this->post('/api/login', [
            'email' => "admin@email.com",
            'password' => "password"
        ]);

        $response->assertStatus(200);
    }

    public function test_login_with_wrong_email(): void
    {
        $response = $this->post('/api/login', [
            'email' => "admi1n@email.com",
            'password' => "password"
        ]);

        $response->assertStatus(400);
    }

    public function test_login_with_wrong_password(): void
    {
        $response = $this->post('/api/login', [
            'email' => "admin@email.com",
            'password' => "p4assword"
        ]);

        $response->assertStatus(400);
    }
}
