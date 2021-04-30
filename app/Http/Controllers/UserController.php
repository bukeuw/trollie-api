<?php

namespace App\Http\Controllers;

use App\Card;
use App\ListModel;
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

    public function getNotifications($userId)
    {
        $user = User::findOrFail($userId);
        $notifications = $user->notifications->map(function ($notif, $key) {
            $fromList = ListModel::find($notif->data['from']);
            $toList = ListModel::find($notif->data['to']);
            $card = Card::find($notif->data['card_id']);
            $user = User::find($notif->data['user_id']);

            return [
                'id' => $notif->id,
                'card_title' => $card->title,
                'from_title' => $fromList->title,
                'to_title' => $toList->title,
                'user' => $user->name,
            ];
        });

        return response()->json([
            'data' => $notifications,
        ]);
    }
}
