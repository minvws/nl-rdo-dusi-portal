<?php

declare(strict_types=1);

use MinVWS\DUSi\User\Admin\API\Http\Controllers\HomeController;
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
});
