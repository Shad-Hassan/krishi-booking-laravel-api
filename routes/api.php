<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Api\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\BankAccountController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/bank-accounts', [PaymentController::class, 'bankAccounts']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Booking routes
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/{id}', [BookingController::class, 'show']);
    Route::get('/bookings/{id}/payments', [BookingController::class, 'payments']);
    Route::get('/bookings/{id}/payment-summary', [BookingController::class, 'paymentSummary']);
    Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel']);

    // Payment routes
    Route::post('/payments', [PaymentController::class, 'store']);

    // Admin routes (require admin role)
    Route::prefix('admin')->middleware('admin')->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::get('/dashboard/recent-activity', [DashboardController::class, 'recentActivity']);

        // Bookings management
        Route::get('/bookings', [AdminBookingController::class, 'index']);
        Route::get('/bookings/{id}', [AdminBookingController::class, 'show']);
        Route::put('/bookings/{id}/activate', [AdminBookingController::class, 'activate']);
        Route::put('/bookings/{id}/process-cancellation', [AdminBookingController::class, 'processCancellation']);

        // Payments management
        Route::get('/payments', [AdminPaymentController::class, 'index']);
        Route::put('/payments/{id}/verify', [AdminPaymentController::class, 'verify']);
        Route::put('/payments/{id}/reject', [AdminPaymentController::class, 'reject']);

        // Bank accounts management
        Route::get('/bank-accounts', [BankAccountController::class, 'index']);
        Route::post('/bank-accounts', [BankAccountController::class, 'store']);
        Route::put('/bank-accounts/{id}', [BankAccountController::class, 'update']);
        Route::delete('/bank-accounts/{id}', [BankAccountController::class, 'destroy']);
        Route::put('/bank-accounts/{id}/toggle-status', [BankAccountController::class, 'toggleStatus']);
    });
});
