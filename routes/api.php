<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\SubsidyStageController;
use App\Http\Controllers\SubsidyController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (prefixed with /api/)
|--------------------------------------------------------------------------
*/

Route::get('subsidies', [SubsidyController::class, 'index'])->name('subsidy-list');

Route::get('forms/{form}', [SubsidyStageController::class, 'show'])->name('form-show');

Route::middleware('auth')->group(
    function () {
        Route::post('forms/{form}/applications', [ApplicationController::class, 'createDraft'])->name('application-create-draft');
        Route::put('applications/{application}', [ApplicationController::class, 'submit'])->name('application-submit');
        Route::post('applications/{application}/files', [ApplicationController::class, 'uploadFile'])->name('application-upload-file');

        Route::get('user/info', [UserController::class, 'info'])->name('user-info');
        Route::post('user/logout', [UserController::class, 'logout'])->name('user-logout');
    }
);
