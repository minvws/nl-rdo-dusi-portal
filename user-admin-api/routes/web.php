<?php

declare(strict_types=1);

use MinVWS\DUSi\User\Admin\API\Http\Controllers\HomeController;
use MinVWS\DUSi\User\Admin\API\Http\Controllers\UserController;
use MinVWS\DUSi\User\Admin\API\Http\Controllers\UserRolesController;
use MinVWS\DUSi\User\Admin\API\Http\Controllers\OrganisationController;
use MinVWS\DUSi\User\Admin\API\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/', HomeController::class)->name('home');
    Route::get('/account', UserProfileController::class)->name('profile.show');

    Route::resource('organisations', OrganisationController::class)
        ->only(['index', 'create', 'store', 'show', 'update', 'destroy']);

    Route::get('/users/{user}/credentials', [UserController::class, 'credentials'])
        ->name('users.credentials');
    Route::post('/users/{user}/reset-credentials', [UserController::class, 'resetCredentials'])
        ->name('users.reset-credentials');
    Route::put('/users/{user}/active', [UserController::class, 'updateActive'])
        ->name('users.update-active');

    Route::resource('users', UserController::class)
        ->only(['index', 'create', 'store', 'show', 'update', 'destroy']);
    Route::resource('users.roles', UserRolesController::class)
        ->only(['index', 'store', 'destroy']);
});
