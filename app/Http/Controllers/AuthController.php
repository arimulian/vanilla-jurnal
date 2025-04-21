<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function login(UserRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'email or password is wrong'
            ], 400);
        }
        $token = $user->createToken('auth-token', ['*'], now()->addWeek())->plainTextToken;
        return response()->json([
            'user' => new UserResource($user),
            'token' => $token
        ]);
    }

    public function unauthenticated()
    {
        return response()->json([
            "status" => false,
            "message" => "Unauthenticated. Please login first",
        ], 401);
    }

    public function logout(): JsonResponse
    {
        $user = Auth::user();
        $user->tokens()->delete();
        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
