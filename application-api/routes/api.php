<?php

declare(strict_types=1);

use MinVWS\DUSi\Application\API\Http\Controllers\ActionableController;
use MinVWS\DUSi\Application\API\Http\Controllers\ApplicationController;
use MinVWS\DUSi\Application\API\Http\Controllers\MessageController;
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

        Route::get('applications', [ApplicationController::class, 'index']);
        Route::get('messages', [MessageController::class, 'index']);
        Route::get('actionables/counts', [ActionableController::class, 'counts']);

        Route::get('user/info', [UserController::class, 'info'])->name('user-info');
        Route::post('user/logout', [UserController::class, 'logout'])->name('user-logout');

        // TODO: route name not suitable for user messages
        Route::get('ui/applications/messages-filter', [MessageController::class, 'showFilters']);
    }
);

Route::get('btv', [MockedResourceController::class, 'btv']);
