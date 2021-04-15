<?php

namespace Tests\Feature;

use App\Board;
use App\ListModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class ListTest extends TestCase
{
    use WithoutMiddleware, RefreshDatabase, WithFaker;

    public function testList()
    {
        $board = factory(Board::class)->create();
        factory(ListModel::class, 10)->create([
            'board_id' => $board->id,
        ]);

        $response = $this->getJson("/api/lists?board_id=$board->id");

        $response->assertStatus(200);
        $response->assertJson([
            'data' => $board->lists->toArray(),
        ]);
    }

    public function testGetList()
    {
        factory(Board::class)->create();
        $lists = factory(ListModel::class, 10)->create();
        $firstList = $lists->first();

        $response = $this->getJson("/api/lists/$firstList->id");
        $response->assertStatus(200);
        $response->assertJson([
            'data' => $firstList->toArray(),
        ]);
    }

    public function testCreateList()
    {
        $board = factory(Board::class)->create();
        $data = [
            'title' => $this->faker->sentence,
            'board_id' => $board->id,
        ];

        $response = $this->postJson('/api/lists', $data);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
        ]);
        $this->assertDatabaseHas('lists', $data);
    }

    public function testUpdateList()
    {
        factory(Board::class)->create();
        $list = factory(ListModel::class)->create([
            'title' => 'List title',
        ]);

        $data = [
            'title' => $this->faker->sentence,
        ];

        $response = $this->patchJson("/api/lists/$list->id", $data);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
        ]);
        $this->assertDatabaseHas('lists', $data);

        $list->refresh();
        $this->assertEquals($data['title'], $list->title);
    }
}
