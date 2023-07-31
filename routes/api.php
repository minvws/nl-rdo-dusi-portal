<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicationController;

/*
|--------------------------------------------------------------------------
| API Routes (prefixed with /api/)
|--------------------------------------------------------------------------
*/

Route::get('/applications', [ApplicationController::class, 'index']);
Route::post('/applications', [ApplicationController::class, 'store']);
Route::get('/applications/{application}', [ApplicationController::class, 'show']);
Route::put('/applications/{application}', [ApplicationController::class, 'update']);
Route::delete('/applications/{application}', [ApplicationController::class, 'destroy']);
Route::get('/applicationsFilter', [ApplicationController::class, 'getFilteredApplications']);
