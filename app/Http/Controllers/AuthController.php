<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Register
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        $respData = [
            'status'    => "success",
            'message'   => 'Register user berhasil',
            'data'      => [
                'user' => $user,
                'token' => $token
            ]
        ];

        return response()->json($respData, 201);
    }

    // Login
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // Cek user
        $user = User::where('email', $fields['email'])->first();

        // Cek password
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response()->json([
                'status'    => "failed",
                'message'   => 'Invalid credentials, username / password salah'
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $respData = [
            'status'    => "success",
            'message'   => 'Login berhasil berhasil',
            'data'      => [
                'user' => $user,
                'token' => $token
            ]
        ];

        return response()->json($respData, 200);
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status'    => "success",
            'message'   => 'Logged out'
        ]);
    }
}
