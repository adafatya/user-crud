<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateUserTest extends TestCase
{
    public function test_request_without_token(): void
    {
        $response = $this->put('/api/users/2');
 
        $response->assertStatus(401);
    }

    public function test_request_with_wrong_token(): void
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer abcde"
        ])->put('/api/users/2');
 
        $response->assertStatus(401);
    }

    public function test_request_as_user_1(): void
    {
        $token = auth()->tokenById(1);
        
        $response = $this->withHeaders([
            'Authorization' => "Bearer ".$token
        ])->put('/api/users/2', [
            'name' => 'test1',
            'email' => 'test01@email.com',
            'password' => "secret",
            'age' => 20,
            'membership_status_id' => 2,
        ]);

        $response->assertStatus(200);
    }

    public function test_request_as_user_1_with_invalid_email(): void
    {
        $token = auth()->tokenById(1);
        
        $response = $this->withHeaders([
            'Authorization' => "Bearer ".$token
        ])->put('/api/users/2', [
            'name' => 'test1',
            'email' => 'test01',
            'password' => "secret",
            'age' => 20,
            'membership_status_id' => 2,
        ]);

        $response->assertStatus(400);
    }

    public function test_request_as_user_1_with_invalid_age(): void
    {
        $token = auth()->tokenById(1);
        
        $response = $this->withHeaders([
            'Authorization' => "Bearer ".$token
        ])->put('/api/users/2', [
            'name' => 'test1',
            'email' => 'test01@email.com',
            'password' => "secret",
            'age' => -20,
            'membership_status_id' => 2,
        ]);

        $response->assertStatus(400);
    }

    public function test_request_as_user_1_with_invalid_membership_status_id(): void
    {
        $token = auth()->tokenById(1);
        
        $response = $this->withHeaders([
            'Authorization' => "Bearer ".$token
        ])->put('/api/users/2', [
            'name' => 'test1',
            'email' => 'test01@email.com',
            'password' => "secret",
            'age' => 20,
            'membership_status_id' => -2,
        ]);

        $response->assertStatus(400);
    }
}
