<?php

namespace App\Http\Controllers;

use App\Board;
use App\Http\Requests\ListRequest;
use App\ListModel;
use Illuminate\Http\Request;

class ListController extends Controller
{
    public function index(Request $request)
    {
        $lists = new ListModel();
        $boardId = $request->query('board_id', null);
        $board = null;

        if (!$boardId) {
            $lists = ListModel::all();
        } else {
            $board = Board::findOrFail($boardId);
            $lists = $board->lists;
        }

        return response()->json([
            'data' => $lists,
        ]);
    }

    public function show($listId)
    {
        $list = ListModel::findOrFail($listId);

        return response()->json([
            'data' => $list,
        ]);
    }

    public function store(ListRequest $request)
    {
        $list = ListModel::create([
            'title' => $request->title,
            'board_id' => $request->board_id,
        ]);

        if (!$list) {
            return response()->json([
                'error' => 'Cannot create list',
            ]);
        }

        return response()->json([
            'data' => $list,
        ]);
    }

    public function update(ListRequest $request, $listId)
    {
        $list = ListModel::findOrFail($listId);

        $updated = $list->update([
            'title' => $request->title,
        ]);

        if (!$updated) {
            return response()->json([
                'error' => 'Cannot update list',
            ]);
        }

        return response()->json([
            'data' => $list,
        ]);
    }
}
