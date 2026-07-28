<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DistributionController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VaccineController;
use App\Http\Controllers\VaccineRequestController;
use Illuminate\Support\Facades\Route;

// ── Auth (public) ──
Route::post('/login', [AuthController::class, 'login']);

// ── Everything below requires a logged-in session ──
Route::middleware('require.login')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::apiResource('suppliers', SupplierController::class);
    Route::apiResource('vaccines', VaccineController::class);
    Route::apiResource('facilities', FacilityController::class);
    Route::apiResource('stock', StockController::class);
    Route::apiResource('distribution', DistributionController::class);
    Route::post('/distribution/request', [DistributionController::class, 'requestRestock']);
    Route::apiResource('users', UserController::class);
    Route::get('/vaccine-requests', [VaccineRequestController::class, 'index']);
    Route::post('/vaccine-requests', [VaccineRequestController::class, 'store']);
    Route::put('/vaccine-requests/{id}', [VaccineRequestController::class, 'update']);

    // ── NEW: Audit Log ──
    Route::get('/audit-logs', [AuditLogController::class, 'index']);

    // ── NEW: Stock Transfers ──
    Route::get('/stock-transfers', [StockTransferController::class, 'index']);
    Route::post('/stock-transfers', [StockTransferController::class, 'store']);
});
