<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\ApplicationController;

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

Route::get('/', function () {
    return view('welcome');
});

// Route::post('/login', 'App\Http\Controllers\Auth\LoginController@login');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

// Change the language of the page
Route::get('ChangeLanguage/{locale}', function ($locale) {
    if (in_array($locale, Config::get('app.locales'))) {
        session(['locale' => $locale]);
    }

    return redirect()->back();
})->name('changelang');

// Ony out of Auth for testing reasons
Route::get(
    '/applications',
    [ApplicationController::class, 'index']
)->name('applications.index');
Route::get(
    '/applications/{application}',
    [ApplicationController::class, 'show']
)->name('applications.show');
Route::post(
    '/applications/{application}/update',
    [ApplicationController::class, 'update']
)->name('applications.update');

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('private');
    });
});
