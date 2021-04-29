<?php

namespace App\Http\Controllers;

use App\Card;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $cardId = $request->query('card_id', null);
        $users = null;

        if ($cardId) {
            $card = Card::findOrFail($cardId);
            $users = $card->users;
        } else {
            $users = User::all();
        }

        return response()->json([
            'data' => $users,
        ]);
    }
}
