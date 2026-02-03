<?php

use Illuminate\Support\Facades\Route;

// SPA catch-all route - all routes handled by Vue Router
// Exclude API routes from catch-all
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api).*$');
