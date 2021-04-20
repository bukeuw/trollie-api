<?php

namespace App\Http\Controllers;

use App\Card;
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

        return response()->json([
            'data' => $cards,
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
}
