<?php

namespace Modules\Auth\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Google\Client as GoogleClient;

class AuthController extends Controller
{
   public function login(Request $request)
{
    $credentials = $request->only('username', 'password');

    if (!Auth::attempt($credentials)) {
        return response()->json(['message' => 'Login gagal'], 401);
    }

    $user = Auth::user();

    // Hapus semua token lama sebelum buat token baru
    $user->tokens()->delete();

    $token = $user->createToken('api_token')->plainTextToken;

    return response()->json([
        'message' => 'Login berhasil',
        'access_token' => $token,
        'token_type' => 'Bearer',
        'user' => $user,
    ]);
}
public function updateFcmToken(Request $request)
{
    $request->validate([
        'fcm_token' => 'required|string',
    ]);

    $user = $request->user();
    $user->fcm_token = $request->fcm_token;
    $user->save();

    return response()->json(['message' => 'FCM token berhasil diperbarui']);
}

public function logout(Request $request)
{
    $user = $request->user();

    $user->fcm_token = null;
    $user->save();

    $user->currentAccessToken()->delete();

    return response()->json(['message' => 'Logout berhasil']);
}


}
