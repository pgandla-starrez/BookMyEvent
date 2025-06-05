<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\EventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Event routes
Route::get('/events/{id}', [EventController::class, 'show']);

// Booking routes
Route::post('/bookings', [BookingController::class, 'store']);
