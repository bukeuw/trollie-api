<?php

namespace Tests\Feature;

use App\Card;
use App\ListModel;
use Carbon\Carbon;
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
        $cards = $list->cards->map(function ($card, $key) {
            $users = $card->users->map(function ($user, $key) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                ];
            });

            $statuses = $card->statuses->map(function ($status, $key) {
                return [
                    'id' => $status->id,
                    'title' => $status->title,
                    'color_classes' => $status->color_classes,
                ];
            });

            return [
                'id' => $card->id,
                'title' => $card->title,
                'description' => $card->description,
                'due_date' => $card->due_date,
                'list_id' => $card->list_id,
                'statuses' => $statuses->toArray(),
                'users' => $users->toArray(),
            ];
        });
        $response->assertJson([
            'data' => $cards->toArray(),
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

    public function testAddDueDate()
    {
        factory(ListModel::class)->create();
        $card = factory(Card::class)->create();
        $data = [
            'due_date' => Carbon::now()->addHours(4)->toString(),
        ];

        $response = $this->postJson("/api/cards/$card->id/due-date", $data);
        $response->assertStatus(200);

        $card->refresh();
        $this->assertEquals($data['due_date'], $card->due_date);
    }

    public function testRemoveDueDate()
    {
        factory(ListModel::class)->create();
        $card = factory(Card::class)->create([
            'due_date' => Carbon::now()->addHours(4),
        ]);

        $response = $this->deleteJson("/api/cards/$card->id/due-date");
        $response->assertStatus(200);

        $card->refresh();
        $this->assertNull($card->due_date);
    }
}
