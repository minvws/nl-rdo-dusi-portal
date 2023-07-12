<?php

use App\Http\Controllers\FormController;
use App\Http\Controllers\SubsidyController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (prefixed with /api/)
|--------------------------------------------------------------------------
*/

Route::get('subsidies', [SubsidyController::class, 'index'])->name('subsidy-list');
Route::get('forms/{id}', [FormController::class, 'show'])->name('form-show');
Route::post('forms/{id}', [FormController::class, 'submit'])->name('form-submit');
Route::post('forms/{id}/files', [FormController::class, 'uploadFile'])->name('form-upload-file');

Route::get('user/info', [UserController::class, 'info'])->name('user-info');
Route::post('user/logout', [UserController::class, 'logout'])->name('user-logout');
