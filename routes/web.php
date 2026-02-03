<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

// One-time admin creation (only when CREATE_ADMIN_SECRET is set in env)
Route::get('/create-admin', function () {
    $secret = config('services.create_admin_secret');
    if (config('app.env') !== 'production' || ! $secret || request('secret') !== $secret) {
        return response('Missing or wrong secret. In Render: Environment → add CREATE_ADMIN_SECRET (e.g. unijob2026admin), Save, wait for redeploy, then visit this URL with ?secret=unijob2026admin', 403);
    }
    $email = request('email', 'admin@unijob.com');
    $password = request('password', 'unijobadmin123');
    if (User::where('email', $email)->exists()) {
        return response('Admin with this email already exists. Use that account or a different email.', 200);
    }
    // If old default admin exists, update to new credentials
    $oldAdmin = User::where('email', 'admin@example.com')->where('role', 'admin')->first();
    if ($oldAdmin && $email === 'admin@unijob.com') {
        $oldAdmin->update(['email' => $email, 'password' => Hash::make($password)]);
        return response('Admin updated to ' . $email . '. You can now log in with that and unijobadmin123. Remove CREATE_ADMIN_SECRET from Render Environment and redeploy.', 200);
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
// Exclude API and create-admin from catch-all
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api|create-admin).*$');
