<?php

use App\Http\Controllers\AllotmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TerminalController;
use App\Http\Controllers\TypeManagementController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes
Route::middleware(['is_user_auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Common Routes (View only for normal users, CRUD for admin handled in controllers)
    Route::get('/warehouses', [WarehouseController::class, 'index']);
    Route::get('/branches', [BranchController::class, 'index']);
    Route::get('/terminals', [TerminalController::class, 'index']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::get('/type-management', [TypeManagementController::class, 'index']);
    Route::get('/allotments', [AllotmentController::class, 'index']);

    // Client Search
    Route::get('/clients/search', [\App\Http\Controllers\ClientController::class, 'search']);

    // Movement Routes
    Route::post('/movements/incoming', [MovementController::class, 'incoming']);
    Route::post('/movements/outgoing', [MovementController::class, 'outgoing']);

    // Admin Only Routes
    Route::middleware(['is_admin'])->group(function () {
        // Users
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users', [UserController::class, 'index']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);

        // Warehouses
        Route::post('/warehouses', [WarehouseController::class, 'store']);
        Route::put('/warehouses/{id}', [WarehouseController::class, 'update']);
        Route::delete('/warehouses/{id}', [WarehouseController::class, 'destroy']);

        // Branches
        Route::post('/branches', [BranchController::class, 'store']);
        Route::put('/branches/{id}', [BranchController::class, 'update']);
        Route::delete('/branches/{id}', [BranchController::class, 'destroy']);

        // Terminals
        Route::post('/terminals', [TerminalController::class, 'store']);
        Route::put('/terminals/{id}', [TerminalController::class, 'update']);
        Route::delete('/terminals/{id}', [TerminalController::class, 'destroy']);
        Route::put('/terminals/{terminalId}/series/{seriesId}', [TerminalController::class, 'updateSeries']);

        // Products
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    });
});
