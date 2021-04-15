<?php

namespace Tests\Feature;

use App\Card;
use App\ListModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class CardTest extends TestCase
{
    use WithoutMiddleware, RefreshDatabase, WithFaker;

    public function testListCard()
    {
        $list = factory(ListModel::class)->create();
        factory(Card::class, 10)->create([
            'list_id' => $list->id,
        ]);

        $response = $this->getJson("/api/cards?list_id=$list->id");

        $response->assertStatus(200);
        $response->assertJson([
            'data' => $list->cards->toArray(),
        ]);
    }

    public function testGetCard()
    {
        $list = factory(ListModel::class)->create();
        $cards = factory(Card::class, 10)->create([
            'list_id' => $list->id,
        ]);
        $firstCard = $cards->first();

        $response = $this->getJson("/api/cards/$firstCard->id");
        $response->assertStatus(200);
        $response->assertJson([
            'data' => $firstCard->toArray(),
        ]);
    }

    public function testCreateCard()
    {
        $list = factory(ListModel::class)->create();
        $data = [
            'title' => $this->faker->sentence,
            'list_id' => $list->id,
        ];

        $response = $this->postJson('/api/cards', $data);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
        ]);
        $this->assertDatabaseHas('cards', $data);
    }

    public function testUpdateCard()
    {
        $list = factory(ListModel::class)->create();
        $card = factory(Card::class)->create();
        $data = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'list_id' => $list->id,
        ];

        $response = $this->patchJson("/api/cards/$card->id", $data);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
        ]);
        $this->assertDatabaseHas('cards', $data);

        $card->refresh();
        $this->assertEquals($data, [
            'title' => $card->title,
            'description' => $card->description,
            'list_id' => $card->list_id,
        ]);
    }
}
