<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetUserDataTest extends TestCase
{
    public function test_request_without_token(): void
    {
        $response = $this->get('/api/users/1');
 
        $response->assertStatus(401);
    }

    public function test_request_with_wrong_token(): void
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer abcde"
        ])->get('/api/users/1');
 
        $response->assertStatus(401);
    }

    public function test_request_as_user_1(): void
    {
        $token = auth()->tokenById(1);
        
        $response = $this->withHeaders([
            'Authorization' => "Bearer ".$token
        ])->get('/api/users/1');

        $response->assertStatus(200);
    }
}
