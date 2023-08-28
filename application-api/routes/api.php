<?php

declare(strict_types=1);

use MinVWS\DUSi\Application\API\Http\Controllers\ApplicationController;
use MinVWS\DUSi\Application\API\Http\Controllers\MockedResourceController;
use MinVWS\DUSi\Application\API\Http\Controllers\SubsidyStageController;
use MinVWS\DUSi\Application\API\Http\Controllers\SubsidyController;
use MinVWS\DUSi\Application\API\Http\Controllers\UserController;
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
        Route::post('forms/{form}/applications', [ApplicationController::class, 'createDraft'])
            ->name('application-create-draft');
        Route::put('applications/{application}', [ApplicationController::class, 'submit'])
            ->name('application-submit');
        Route::post('applications/{application}/files', [ApplicationController::class, 'uploadFile'])
            ->name('application-upload-file');

        Route::get('user/info', [UserController::class, 'info'])->name('user-info');
        Route::post('user/logout', [UserController::class, 'logout'])->name('user-logout');
    }
);

//TODO: Remove mocked routes when the real API is ready
Route::get('messages', [MockedResourceController::class, 'messages']);
Route::get('requests', [MockedResourceController::class, 'requests']);
Route::get('btv', [MockedResourceController::class, 'btv']);
