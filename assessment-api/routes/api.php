<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use MinVWS\DUSi\Assessment\API\Http\Controllers\ApplicationController;

/*
|--------------------------------------------------------------------------
| API Routes (prefixed with /api/)
|--------------------------------------------------------------------------
*/

Route::get('/applications/{application}', [ApplicationController::class, 'show']);
Route::get('/applications', [ApplicationController::class, 'filterApplications']);

Route::get('/ui/applications/count', [ApplicationController::class, 'getApplicationsCount']);
Route::get(
    '/ui/applications/messages-filter',
    [ApplicationController::class, 'getApplicationMessageFilterResource']
);
Route::get(
    '/ui/applications/requests-filter',
    [ApplicationController::class, 'getApplicationRequestFilterForUserResource']
);
Route::get(
    '/ui/applications/all-requests-filter',
    [ApplicationController::class, 'getApplicationRequestFilterResource']
);
