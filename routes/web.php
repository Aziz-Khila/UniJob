<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

// One-time admin creation (only when CREATE_ADMIN_SECRET is set in env)
Route::get('/create-admin', function () {
    $secret = env('CREATE_ADMIN_SECRET');
    if (config('app.env') !== 'production' || ! $secret || request('secret') !== $secret) {
        abort(404);
    }
    $email = request('email', 'admin@example.com');
    $password = request('password', 'admin123');
    if (User::where('email', $email)->exists()) {
        return response('Admin with this email already exists. Use that account or a different email.', 200);
    }
    User::create([
        'name' => 'Admin User',
        'email' => $email,
        'password' => Hash::make($password),
        'role' => 'admin',
        'identity_card_number' => '00000000',
        'address' => 'Admin Address',
        'last_3_numbers' => '000',
        'verification_status' => 'approved',
    ]);
    return response('Admin created. Email: ' . $email . '. Now remove CREATE_ADMIN_SECRET from Render Environment and redeploy.', 200);
})->name('create-admin');

// SPA catch-all route - all routes handled by Vue Router
// Exclude API routes from catch-all
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api).*$');
