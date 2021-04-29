<?php

namespace App\Http\Controllers;

use App\Card;
use App\Http\Requests\CardDueDateRequest;
use App\Http\Requests\CardRequest;
use App\Http\Requests\CardUpdateRequest;
use App\ListModel;
use Illuminate\Http\Request;

class CardController extends Controller
{
    public function index(Request $request)
    {
        $listId = $request->query('list_id', null);
        $cards = null;

        if ($listId) {
            $list = ListModel::findOrFail($listId);
            $cards = $list->cards;
        } else {
            $cards = Card::all();
        }

        $cards = $cards->map(function ($card, $key) {
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

        return response()->json([
            'data' => $cards->toArray(),
        ]);
    }

    public function show($cardId)
    {
        $card = Card::findOrFail($cardId);

        return response()->json([
            'data' => $card,
        ]);
    }

    public function store(CardRequest $request)
    {
        $card = Card::create([
            'title' => $request->title,
            'description' => '',
            'list_id' => $request->list_id,
        ]);

        if (!$card) {
            return response()->json([
                'error' => 'Cannot save card',
            ], 400);
        }

        return response()->json([
            'data' => $card,
        ]);
    }

    public function update(CardUpdateRequest $request, $cardId)
    {
        $card = Card::findOrFail($cardId);

        if ($request->has('title')) {
            $card->title = $request->title;
        }

        if ($request->has('description')) {
            $card->description = $request->description;
        }

        if ($request->has('list_id')) {
            $card->list_id = $request->list_id;
        }

        $card->save();

        return response()->json([
            'data' => $card,
        ]);
    }

    public function addDueDate(CardDueDateRequest $request, $cardId)
    {
        $card = Card::findOrFail($cardId);

        $isUpdated = $card->update([
            'due_date' => $request->due_date,
        ]);

        if (!$isUpdated) {
            return response()->json([
                'error' => 'cannot set due date',
            ], 400);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function removeDueDate($cardId)
    {
        $card = Card::findOrFail($cardId);

        $isUpdated = $card->update([
            'due_date' => null,
        ]);

        if (!$isUpdated) {
            return response()->json([
                'error' => 'cannot remove due date',
            ], 400);
        }

        return response()->json([
            'success' => true,
        ]);
    }
}
