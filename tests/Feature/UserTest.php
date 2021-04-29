<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class UserTest extends TestCase
{
    use WithoutMiddleware, RefreshDatabase, WithFaker;

    public function testExample()
    {
        $users = factory(User::class, 10)->create();

        $response = $this->getJson('/api/users');

        $response->assertStatus(200);
        $response->assertJson([
            'data' => $users->toArray(),
        ]);
    }
}
