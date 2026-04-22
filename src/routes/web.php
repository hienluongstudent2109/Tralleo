<?php

use Illuminate\Support\Facades\Route;

// Health check route
Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

// SPA routes - All other routes are handled by Vue Router
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
