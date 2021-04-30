<?php

namespace Tests\Feature;

use App\Card;
use App\ListModel;
use App\Notifications\CardMoveNotification;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Notification;
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

        $cards = $list->cards;
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

        $users = $firstCard->users->map(function ($user, $key) {
            return [
                'id' => $user->id,
                'name' => $user->name,
            ];
        });

        $statuses = $firstCard->statuses->map(function ($status, $key) {
            return [
                'id' => $status->id,
                'title' => $status->title,
                'color_classes' => $status->color_classes,
            ];
        });
        $response->assertJson([
            'data' => [
                'id' => $firstCard->id,
                'title' => $firstCard->title,
                'description' => $firstCard->description,
                'due_date' => $firstCard->due_date,
                'list_id' => $firstCard->list_id,
                'statuses' => $statuses->toArray(),
                'users' => $users->toArray(),
            ],
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
        Notification::fake();
        $user = factory(User::class)->create();
        $list = factory(ListModel::class)->create();
        $card = factory(Card::class)->create();
        $data = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'list_id' => $list->id,
        ];

        $response = $this->actingAs($user)
                         ->patchJson("/api/cards/$card->id", $data);
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
        Notification::assertNothingSent();
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

    public function testMemberJoinCard()
    {
        factory(ListModel::class)->create();
        $user = factory(User::class)->create();
        $card = factory(Card::class)->create();

        $data = [
            'user_id' => $user->id,
        ];

        $response = $this->postJson("/api/cards/$card->id/membership", $data);
        $response->assertStatus(200);

        $this->assertDatabaseHas('card_user', [
            'card_id' => $card->id,
            'user_id' => $user->id,
        ]);
    }

    public function testMemberLeaveCard()
    {
        factory(ListModel::class)->create();
        $user = factory(User::class)->create();
        $card = factory(Card::class)->create();
        $card->users()->attach($user->id);

        $data = [
            'user_id' => $user->id,
        ];

        $response = $this->postJson("/api/cards/$card->id/membership", $data);
        $response->assertStatus(200);

        $this->assertDatabaseMissing('card_user', [
            'card_id' => $card->id,
            'user_id' => $user->id,
        ]);
    }

    public function testMoveCardNotification()
    {
        Notification::fake();
        $users = factory(User::class, 10)->create();
        $firstUser = $users->first();
        $list = factory(ListModel::class)->create();
        $card = factory(Card::class)->create();
        $data = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'list_id' => $list->id,
        ];

        $users->each(function ($user, $key) use ($card) {
            $card->users()->attach($user->id);
        });

        $response = $this->actingAs($firstUser)
                         ->patchJson("/api/cards/$card->id", $data);
        $response->assertJson([
            'data' => [],
        ]);
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

        $senderList = $card->users()
             ->where('user_id', '<>', auth()->user()->id)
             ->get();

        Notification::assertSentTo(
            $senderList,
            CardMoveNotification::class
        );
    }
}
