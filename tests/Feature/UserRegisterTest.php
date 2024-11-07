<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserRegisterTest extends TestCase
{
    public function test_register_with_valid_data(): void
    {
        $response = $this->post('/api/register', [
            'name' => 'test',
            'email' => 'test1@email.com',
            'password' => "password",
            'age' => 18,
            'membership_status_id' => 1,
        ]);

        $response->assertStatus(201);
    }

    public function test_register_with_invalid_email(): void
    {
        $response = $this->post('/api/register', [
            'name' => 'test',
            'email' => 'test1',
            'password' => "password",
            'age' => 18,
            'membership_status_id' => 1,
        ]);

        $response->assertStatus(400);
    }

    public function test_register_with_invalid_age(): void
    {
        $response = $this->post('/api/register', [
            'name' => 'test',
            'email' => 'test1@email.com',
            'password' => "password",
            'age' => -18,
            'membership_status_id' => 1,
        ]);

        $response->assertStatus(400);
    }
}
