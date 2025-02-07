<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use MinVWS\DUSi\Application\API\Http\Controllers\SystemHealthController;

/*
|--------------------------------------------------------------------------
| API Routes (prefixed with /noc/)
|--------------------------------------------------------------------------
*/

Route::get('health', [SystemHealthController::class, 'index']);
