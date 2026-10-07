<?php

declare(strict_types=1);

use App\Presentation\Http\Controller\AuthController;
use App\Presentation\Http\Controller\CategoryController;
use App\Presentation\Http\Controller\HealthController;
use App\Presentation\Http\Controller\MediaController;
use App\Presentation\Http\Controller\ProductController;
use App\Presentation\Http\Controller\ReportController;
use App\Presentation\Http\Controller\SaleController;
use App\Presentation\Middleware\AuthenticateToken;
use App\Presentation\Middleware\RequireRole;
use Illuminate\Support\Facades\Route;

// E-14 Health
Route::get('/health', [HealthController::class, 'check']);

// E-15 Media
Route::get('/media/{key}', [MediaController::class, 'show']);

// Auth
Route::post('/auth/login', [AuthController::class, 'login']); // E-01

// Protected Routes
Route::middleware(AuthenticateToken::class)->group(function () {
    // Admin user registration (E-02)
    Route::post('/auth/register', [AuthController::class, 'register'])
        ->middleware(RequireRole::class . ':admin');

    // Products
    Route::get('/products', [ProductController::class, 'index']); // E-03
    Route::get('/products/{id}', [ProductController::class, 'show']); // E-04
    Route::post('/products', [ProductController::class, 'store'])->middleware(RequireRole::class . ':admin'); // E-05
    Route::put('/products/{id}', [ProductController::class, 'update'])->middleware(RequireRole::class . ':admin'); // E-06
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->middleware(RequireRole::class . ':admin'); // E-07
    Route::post('/products/{id}/image', [ProductController::class, 'uploadImage'])->middleware(RequireRole::class . ':admin'); // E-08

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']); // E-09

    // Sales
    Route::post('/sales', [SaleController::class, 'store']); // E-10
    Route::get('/sales', [SaleController::class, 'index']); // E-11
    Route::get('/sales/{id}', [SaleController::class, 'show']); // E-12

    // Reports
    Route::get('/reports/sales', [ReportController::class, 'sales']); // E-13
});