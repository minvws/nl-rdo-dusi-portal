<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicationController;

/*
|--------------------------------------------------------------------------
| API Routes (prefixed with /api/)
|--------------------------------------------------------------------------
*/

Route::get('/applications/{application}', [ApplicationController::class, 'show']);
Route::get('/applications', [ApplicationController::class, 'filterApplications']);
