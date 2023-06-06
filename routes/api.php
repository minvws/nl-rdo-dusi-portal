<?php

use App\Http\Controllers\FormController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (prefixed with /api/)
|--------------------------------------------------------------------------
*/

Route::get('forms', [FormController::class, 'index']);
Route::get('forms/{id}', [FormController::class, 'show']);
Route::post('forms/{id}', [FormController::class, 'submit']);
