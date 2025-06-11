<?php

namespace Modules\Auth\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
   public function login(Request $request)
{
    $credentials = $request->only('username', 'password');

    if (!Auth::attempt($credentials)) {
        return response()->json(['message' => 'Login gagal'], 401);
    }

    $user = Auth::user();
    $user->tokens()->delete();

    $token = $user->createToken('api_token')->plainTextToken;

    return response()->json([
        'message' => 'Login berhasil',
        'access_token' => $token,
        'token_type' => 'Bearer',
        'user' => $user,
    ]);
}
public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();

    return response()->json(['message' => 'Logout berhasil']);
}

}
