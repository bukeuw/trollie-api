<?php

namespace App\Http\Controllers;

use App\Board;
use App\Http\Requests\BoardRequest;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    public function index()
    {
        $boards = Board::all();

        return response()->json([
            'data' => $boards,
        ]);
    }

    public function store(BoardRequest $request) {
        $board = Board::create([
            'title' => $request->title,
            'color' => $request->color,
        ]);

        if (!$board) {
            return response()->json([
                'error' => 'Cannot create board',
            ]);
        }

        return response()->json([
            'data' => $board,
        ]);
    }

    public function update(BoardRequest $request, $boardId) {
        $board = Board::findOrFail($boardId);

        $updated = $board->update([
            'title' => $request->title,
            'color' => $request->color,
        ]);

        if (!$updated) {
            return response()->json([
                'error' => 'Cannot update board',
            ]);
        }

        return response()->json([
            'data' => $board,
        ]);
    }
}
