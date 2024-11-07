<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetUsersDataTest extends TestCase
{
    public function test_request_without_token(): void
    {
        $response = $this->get('/api/users');
 
        $response->assertStatus(401);
    }

    public function test_request_with_wrong_token(): void
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer abcde"
        ])->get('/api/users');
 
        $response->assertStatus(401);
    }

    public function test_request_as_user_1(): void
    {
        $token = auth()->tokenById(1);
        
        $response = $this->withHeaders([
            'Authorization' => "Bearer ".$token
        ])->get('/api/users?limit=20&page=1');

        $response->assertStatus(200);
    }

    public function test_request_as_user_1_with_invalid_limit(): void
    {
        $token = auth()->tokenById(1);
        
        $response = $this->withHeaders([
            'Authorization' => "Bearer ".$token
        ])->get('/api/users?limit=-20&page=1');

        $response->assertStatus(400);
    }

    public function test_request_as_user_1_with_invalid_page(): void
    {
        $token = auth()->tokenById(1);
        
        $response = $this->withHeaders([
            'Authorization' => "Bearer ".$token
        ])->get('/api/users?limit=20&page=-1');

        $response->assertStatus(400);
    }
}
