<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Pusher\Pusher;

class AuthController extends Controller
{
    protected $pusher = null;
     /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);

        $this->pusher = new Pusher(
            env('PUSHER_APP_KEY', ''),
            env('PUSHER_APP_SECRET', ''),
            env('PUSHER_APP_ID', ''),
            [
                'useTLS' => true,
                'cluster' => env('PUSHER_APP_CLUSTER', ''),
            ]
        );
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function user()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => 720 * 60,
        ]);
    }

    public function authenticateChannel(Request $request)
    {
        if (Auth::check() && $request->has(['socket_id', 'channel_name'])) {
            $socketId = $request->input('socket_id');
            $channelName = $request->input('channel_name');
            $user = Auth::user();
            $userInfo = [
                'name' => $user->name,
                'email' => $user->email,
            ];

            return $this->pusher->presence_auth($channelName, $socketId, $user->id, $userInfo);
        }

        return response()->json([
            'error' => 'Unauthorized'
        ], 401);
    }
}
