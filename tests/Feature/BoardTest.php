<?php

namespace Tests\Feature;

use App\Board;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class BoardTest extends TestCase
{
    use WithoutMiddleware, RefreshDatabase, WithFaker;

    public function testListBoard()
    {
        $count = 10;
        $boards = factory(Board::class, $count)->create();

        $response = $this->getJson('/api/boards');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => []
        ]);

        $response->assertJson([
            'data' => $boards->toArray(),
        ]);
    }

    public function testCreateBoard()
    {
        $data = [
            'title' => $this->faker->sentence,
            'color' => $this->faker->hexColor,
        ];

        $response = $this->postJson('/api/boards', $data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
        ]);

        $this->assertDatabaseHas('boards', $data);
    }

    public function testUpdateBoard()
    {
        $board = factory(Board::class)->create();

        $data = [
            'title' => $this->faker->sentence,
            'color' => $this->faker->hexColor,
        ];

        $response = $this->patchJson("/api/boards/$board->id", $data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
        ]);

        $this->assertDatabaseHas('boards', $data);
    }

    public function testfindBoardById()
    {
        $boards = factory(Board::class, 10)->create();
        $board = $boards->first();

        $response = $this->getJson("/api/boards/$board->id");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
        ]);
        $response->assertJson([
            'data' => $board->toArray(),
        ]);
    }
}
