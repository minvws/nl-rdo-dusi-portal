<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicationController;

/*
|--------------------------------------------------------------------------
| API Routes (prefixed with /api/)
|--------------------------------------------------------------------------
*/

Route::get('/applications', [ApplicationController::class, 'index']);
Route::get('/application/{application}', [ApplicationController::class, 'show']);
Route::get('/applicationsFilter', [ApplicationController::class, 'getFilteredApplications']);
