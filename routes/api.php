<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\SubsidyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (prefixed with /api/)
|--------------------------------------------------------------------------
*/

Route::get('subsidies', [SubsidyController::class, 'index'])->name('subsidy-list');

Route::get('forms/{form}', [FormController::class, 'show'])->name('form-show');

Route::post('forms/{form}/applications', [ApplicationController::class, 'createDraft'])->name('application-create-draft');
Route::put('applications/{application}', [ApplicationController::class, 'submit'])->name('application-submit');
Route::post('applications/{application}/files', [ApplicationController::class, 'uploadFile'])->name('application-upload-file');
