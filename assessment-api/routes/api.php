<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use MinVWS\DUSi\Assessment\API\Http\Controllers\ApplicationAssessorController;
use MinVWS\DUSi\Assessment\API\Http\Controllers\ApplicationController;
use MinVWS\DUSi\Assessment\API\Http\Controllers\ApplicationFileController;
use MinVWS\DUSi\Assessment\API\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes (prefixed with /api/)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/user', [UserController::class, 'show']);

    Route::prefix('applications')
        ->controller(ApplicationController::class)
        ->group(function () {
            Route::get('/', 'filterApplications');
            Route::get('assigned', 'filterAssignedApplications');
            Route::get('{application}', 'show');
            Route::put('{application}', 'saveAssessment');
            Route::get('{application}/history', 'getApplicationHistory');
            Route::get('{application}/reviewer', 'getApplicationReviewer');
        });

    Route::prefix('applications')
        ->controller(ApplicationFileController::class)
        ->group(function () {
            Route::get('{application}/stages/{applicationStageId}/fields/{fieldCode}/files/{id}', 'show');
        });


    Route::prefix('applications/{application}/assessor')
        ->controller(ApplicationAssessorController::class)
        ->group(function () {
            Route::put('', 'claim');
            Route::delete('', 'release');
        });

    Route::get('/ui/applications/count', [ApplicationController::class, 'getApplicationsCount']);
    Route::get(
        '/ui/applications/messages-filter',
        [ApplicationController::class, 'getApplicationMessageFilterResource']
    );
    Route::get(
        '/ui/applications/cases-filter',
        [ApplicationController::class, 'getApplicationRequestFilterForUserResource']
    );
    Route::get(
        '/ui/applications/all-cases-filter',
        [ApplicationController::class, 'getApplicationRequestFilterResource']
    );
});
