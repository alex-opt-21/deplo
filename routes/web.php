<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LinkedInController;

Route::get('/auth/linkedin', [LinkedInController::class, 'redirect']);
Route::get('/auth/linkedin/callback', [LinkedInController::class, 'callback']);
Route::get('/login', fn() => response()->json(['message' => 'Unauthorized'], 401))->name('login');
Route::get('/', function () {
    return view('welcome');
});
