<?php

declare(strict_types=1);

use MinVWS\DUSi\Application\API\Http\Controllers\ActionableController;
use MinVWS\DUSi\Application\API\Http\Controllers\ApplicationController;
use MinVWS\DUSi\Application\API\Http\Controllers\ApplicationFileController;
use MinVWS\DUSi\Application\API\Http\Controllers\MessageController;
use MinVWS\DUSi\Application\API\Http\Controllers\MockedResourceController;
use MinVWS\DUSi\Application\API\Http\Controllers\SubsidyStageController;
use MinVWS\DUSi\Application\API\Http\Controllers\SubsidyController;
use MinVWS\DUSi\Application\API\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use MinVWS\DUSi\Application\API\Http\Middleware\RequireClientPublicKey;

/*
|--------------------------------------------------------------------------
| API Routes (prefixed with /api/)
|--------------------------------------------------------------------------
*/

Route::get('subsidies', [SubsidyController::class, 'index'])->name('subsidy-list');

Route::get('forms/{form}', [SubsidyStageController::class, 'show'])->name('form-show');

Route::middleware('auth')->group(
    function () {
        Route::get('messages', [MessageController::class, 'index']);

        // TODO: move more routes to this once the frontend is ready
        Route::middleware(RequireClientPublicKey::class)->group(function () {
            Route::post('subsidies/{subsidyCode}/applications', [ApplicationController::class, 'create'])
                ->name('application-create');

            Route::get('applications', [ApplicationController::class, 'index'])
                ->name('application-index');
            Route::put('applications/{reference}', [ApplicationController::class, 'save'])
                ->name('application-save');
            Route::post(
                'applications/{applicationReference}/fields/{fieldCode}/files',
                [ApplicationController::class, 'uploadFile']
            )->name('application-upload-file');
            Route::patch(
                'applications/{reference}/validate',
                [ApplicationController::class, 'validateApplication']
            )->name('application-validate');

            Route::get('applications/{reference}', [ApplicationController::class, 'show']);

            Route::get(
                'applications/{applicationReference}/fields/{fieldCode}/files/{id}',
                [ApplicationFileController::class, 'show']
            );

            Route::get('messages/{id}', [MessageController::class, 'view'])
                ->name('message-view');
            Route::get('messages/{id}/download/{format}', [MessageController::class, 'download'])
                ->name('message-download');
        });

        Route::get('actionables/counts', [ActionableController::class, 'counts'])->name('actionables-count');

        Route::get('user/info', [UserController::class, 'info'])->name('user-info');
        Route::post('user/logout', [UserController::class, 'logout'])->name('user-logout');

        // TODO: route name not suitable for user messages
        Route::get('ui/applications/messages-filter', [MessageController::class, 'showFilters']);
    }
);

Route::get('btv', [MockedResourceController::class, 'btv']);
