<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use MinVWS\DUSi\Assessment\API\Http\Controllers\ApplicationAssessorController;
use MinVWS\DUSi\Assessment\API\Http\Controllers\ApplicationController;
use MinVWS\DUSi\Assessment\API\Http\Controllers\ApplicationExportController;
use MinVWS\DUSi\Assessment\API\Http\Controllers\ApplicationFileController;
use MinVWS\DUSi\Assessment\API\Http\Controllers\ApplicationHashController;
use MinVWS\DUSi\Assessment\API\Http\Controllers\UserController;
use MinVWS\DUSi\Assessment\API\Http\Middleware\EnsurePasswordUpdated;

/*
|--------------------------------------------------------------------------
| API Routes (prefixed with /api/)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [UserController::class, 'show']);
    Route::get('/user/subsidies', [UserController::class, 'subsidies']);

    Route::middleware(EnsurePasswordUpdated::class)->group(function () {
        Route::get('export/applications', [ApplicationExportController::class, 'export']);

        Route::prefix('applications')
            ->controller(ApplicationController::class)
            ->group(function () {
                Route::get('/', 'filterApplications');
                Route::get('assigned', 'filterAssignedApplications');
                Route::get('{application}', 'show');
                Route::put('{application}', 'saveAssessment');
                Route::get('{application}/transition-preview', 'previewTransition');
                Route::patch('{application}/submit', 'submitAssessment');
                Route::get('{application}/history', 'getApplicationHistory');
                Route::get('{application}/reviewer', 'getApplicationReviewer');
                Route::get('{application}/transitions', 'getApplicationTransitions');
            });

        Route::prefix('applications')
            ->controller(ApplicationFileController::class)
            ->group(function () {
                Route::get('{application}/stages/{applicationStageId}/fields/{fieldCode}/files/{id}', 'show');
                Route::post('{application}/stages/{applicationStageId}/fields/{fieldCode}/files', 'uploadFile');
            });


        Route::prefix('applications/{application}/assessor')
            ->controller(ApplicationAssessorController::class)
            ->group(function () {
                Route::put('', 'claim');
                Route::delete('', 'release');
            });
        Route::get('applications/{application}/assessorpool', [
            ApplicationAssessorController::class, 'getAssessorPool'
        ]);
        Route::put('applications/{application}/assign', [ApplicationAssessorController::class, 'assign']);




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

        Route::get('/messages/{message}/download/pdf', [ApplicationController::class, 'getLetterForMessage']);

        Route::get('subsidies/{subsidy}/bankaccounts/duplicates', [
            ApplicationHashController::class, 'getBankAccountDuplicates'
        ]);
    });
});
