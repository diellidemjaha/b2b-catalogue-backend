<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Hashing\BcryptHasher;


use Illuminate\Http\Request;

class AuthController extends Controller
{
 public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $user = \App\Models\User::where('email', $request->email)->first();

    $bcrypt = new \Illuminate\Hashing\BcryptHasher();

    if (!$user || !$bcrypt->check($request->password, $user->password)) {
        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }

    return response()->json([
        'token' => $user->createToken('desktop-app')->plainTextToken,
        'user' => $user
    ]);
}

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['ok' => true]);
    }
}

