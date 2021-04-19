<?php

namespace Tests\Feature;

use App\Card;
use App\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class StatusTest extends TestCase
{
    use WithoutMiddleware, RefreshDatabase, WithFaker;

    public function testListStatus()
    {
        $statuses = factory(Status::class, 10)->create();
        $response = $this->getJson('/api/statuses');

        $response->assertStatus(200);
        $response->assertJson([
            'data' => $statuses->toArray(),
        ]);
    }

    public function testGetStatus()
    {
        $statuses = factory(Status::class, 10)->create();
        $firstStatus = $statuses->first();

        $response = $this->getJson("/api/statuses/$firstStatus->id");

        $response->assertStatus(200);
        $response->assertJson([
            'data' => $firstStatus->toArray(),
        ]);
    }

    public function testCreateStatus()
    {
        $data = [
            'title' => $this->faker->word,
            'color_classes' => 'label-default',
        ];

        $response = $this->postJson('/api/statuses', $data);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
        ]);

        $this->assertDatabaseHas('statuses', $data);
    }

    public function testUpdateStatus()
    {
        $status = factory(Status::class)->create();
        $data = [
            'title' => $this->faker->word,
            'color_classes' => 'label-lime',
        ];

        $response = $this->patchJson("/api/statuses/$status->id", $data);
        $response->assertJson([
            'data' => [],
        ]);
        $response->assertStatus(200);
        $response->assertJsonPath('data.title', $data['title']);
        $response->assertJsonPath('data.color_classes', $data['color_classes']);

        $this->assertDatabaseHas('statuses', $data);
    }

    public function testAddStatusToCard()
    {
        $card = factory(Card::class)->create();
        $status = factory(Status::class)->create();
        $data = [
            'card_id' => $card->id,
            'status_id' => $status->id,
        ];

        $response = $this->postJson('/api/statuses/toggle', $data);
        $response->assertStatus(200);

        $this->assertDatabaseHas('card_status', $data);

        $cardStatus = $card->statuses->first();
        $this->assertEquals($cardStatus->id, $status->id);
    }

    public function testRemoveStatusToCard()
    {
        $card = factory(Card::class)->create();
        $status = factory(Status::class)->create();
        $card->statuses()->attach($status->id);

        $data = [
            'card_id' => $card->id,
            'status_id' => $status->id,
        ];

        $response = $this->postJson('/api/statuses/toggle', $data);
        $response->assertStatus(200);

        $this->assertDatabaseMissing('card_status', $data);
    }
}
