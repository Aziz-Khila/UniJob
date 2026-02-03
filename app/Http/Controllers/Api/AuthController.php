<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'The provided credentials do not match our records.',
            ], 401);
        }

        $user = Auth::user();

        if ($user->banned_at) {
            Auth::logout();
            return response()->json([
                'message' => 'Your account has been banned. Please contact support.',
            ], 403);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        $userData = $user->toArray();
        $userData['average_rating'] = $user->average_rating;
        $userData['total_ratings'] = $user->total_ratings;
        
        // Convert profile_picture path to URL if it exists
        if (!empty($userData['profile_picture']) && !filter_var($userData['profile_picture'], FILTER_VALIDATE_URL)) {
            $userData['profile_picture'] = \Illuminate\Support\Facades\Storage::disk('public')->url($userData['profile_picture']);
        }

        return response()->json([
            'user' => $userData,
            'token' => $token,
            'message' => 'Login successful!',
        ]);
    }

    /**
     * Handle user registration.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role' => ['required', 'in:student,client'],
            'identity_card_number' => ['required', 'string', 'size:8', 'regex:/^[0-9]{8}$/', 'unique:users'],
            'address' => ['required', 'string'],
            'university' => ['nullable', 'required_if:role,student', 'string', 'max:255'],
            'last_3_numbers' => ['required', 'string', 'size:3', 'regex:/^[0-9]{3}$/'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'identity_card_number' => $validated['identity_card_number'],
            'address' => $validated['address'],
            'university' => $validated['university'] ?? null,
            'last_3_numbers' => $validated['last_3_numbers'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        $userData = $user->toArray();
        $userData['average_rating'] = $user->average_rating;
        $userData['total_ratings'] = $user->total_ratings;
        
        // Convert profile_picture path to URL if it exists
        if (!empty($userData['profile_picture']) && !filter_var($userData['profile_picture'], FILTER_VALIDATE_URL)) {
            $userData['profile_picture'] = \Illuminate\Support\Facades\Storage::disk('public')->url($userData['profile_picture']);
        }

        return response()->json([
            'user' => $userData,
            'token' => $token,
            'message' => 'Registration successful!',
        ], 201);
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully!',
        ]);
    }

    /**
     * Get the authenticated user.
     */
    public function user(Request $request)
    {
        $user = $request->user();
        $userData = $user->toArray();
        $userData['average_rating'] = $user->average_rating;
        $userData['total_ratings'] = $user->total_ratings;
        
        // Convert profile_picture path to URL if it exists
        if (!empty($userData['profile_picture']) && !filter_var($userData['profile_picture'], FILTER_VALIDATE_URL)) {
            $userData['profile_picture'] = \Illuminate\Support\Facades\Storage::disk('public')->url($userData['profile_picture']);
        }
        
        return response()->json($userData);
    }
}
