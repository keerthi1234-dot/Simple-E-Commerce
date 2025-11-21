<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // -----------------------------
    // REGISTER USER
    // -----------------------------
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6'
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'User registered successfully!',
            'user'    => $user
        ]);
    }

    // -----------------------------
    // LOGIN USER
    // -----------------------------
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        // Create sanctum token
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful!',
            'token'   => $token
        ]);
    }
}
