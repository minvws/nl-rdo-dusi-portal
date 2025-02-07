<?php

declare(strict_types=1);

use MinVWS\DUSi\Application\API\Http\Controllers\Auth\DigidMockController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

if (config('auth.digid_mock_enabled')) {
    Route::get('oidc/login', [DigidMockController::class, 'login'])->name('digid-mock-login');
}
