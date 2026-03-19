<?php

use App\Http\Controllers\Api\AttendeeController as AttendeeController;
use App\Http\Controllers\Api\EventController as EventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::apiResource('events', EventController::class)->only(['index', 'show']);
Route::apiResource('events', EventController::class)->except(['index', 'show'])->middleware('auth:sanctum');

Route::apiResource('attendees', AttendeeController::class)
->scoped()->except(['update']);