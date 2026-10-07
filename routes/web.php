<?php

declare(strict_types=1);

use App\Presentation\Http\Controller\HealthController;
use App\Presentation\Http\Controller\MediaController;
use Illuminate\Support\Facades\Route;

// E-14 Health (also accessible without /api prefix)
Route::get('/health', [HealthController::class, 'check']);

// E-15 Media (also accessible without /api prefix)
Route::get('/media/{key}', [MediaController::class, 'show']);
