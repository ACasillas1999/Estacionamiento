<?php

use App\Http\Controllers\ParkingDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ParkingDashboardController::class, 'index'])->name('dashboard');
Route::get('/historial', [ParkingDashboardController::class, 'history'])->name('history.index');
Route::post('/spots', [ParkingDashboardController::class, 'storeSpot'])->name('spots.store');
Route::post('/sessions/check-in', [ParkingDashboardController::class, 'checkIn'])->name('sessions.check-in');
Route::patch('/sessions/{parkingSession}/check-out', [ParkingDashboardController::class, 'checkOut'])->name('sessions.check-out');
