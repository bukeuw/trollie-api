<?php

namespace App\Http\Controllers;

use App\Card;
use App\Http\Requests\StatusCreationRequest;
use App\Http\Requests\StatusToggleRequest;
use App\Http\Requests\StatusUpdateRequest;
use App\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function index(Request $request)
    {
        $cardId = $request->query('card_id', null);
        $statuses = null;

        if ($cardId) {
            $card = Card::findOrFail($cardId);
            $statuses = $card->statuses;
        } else {
            $statuses = Status::all();
        }

        return response()->json([
            'data' => $statuses,
        ]);
    }

    public function show($statusId)
    {
        $status = Status::findOrFail($statusId);

        return response()->json([
            'data' => $status,
        ]);
    }

    public function store(StatusCreationRequest $request)
    {
        $status = Status::create([
            'title' => $request->title,
            'color_classes' => $request->color_classes,
        ]);

        if (!$status) {
            return response()->json([
                'error' => 'Failed to create status',
            ], 400);
        }

        return response()->json([
            'data' => $status,
        ]);
    }

    public function update(StatusUpdateRequest $request, $statusId)
    {
        $status = Status::findOrFail($statusId);

        if ($request->has('title')) {
            $status->title = $request->title;
        }

        if ($request->has('color_classes')) {
            $status->color_classes = $request->color_classes;
        }

        $status->save();

        return response()->json([
            'data' => $status,
        ]);
    }

    public function toggleStatus(StatusToggleRequest $request)
    {
        $cardId = $request->card_id;
        $card = Card::findOrFail($cardId);
        $statusId = $request->status_id;

        $hasStatus = $card->statuses()
                          ->where('status_id', $statusId)
                          ->count();

        if ($hasStatus) {
            $card->statuses()->detach($statusId);
        } else {
            $card->statuses()->attach($statusId);
        }

        return response()->json([
            'success' => true,
        ]);
    }
}
