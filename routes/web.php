<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

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

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

// Ony out of Auth for testing reasons
Route::get('/form', [FormController::class, 'index'])->name('form.index');
Route::get('/form/create', [FormController::class, 'create'])->name('form.create');
Route::post('/form', [FormController::class, 'store'])->name('form.store');
Route::get('/form/{form}', [FormController::class, 'show'])->name('form.show');
Route::get('/form/{form}/edit', [FormController::class, 'edit'])->name('form.edit');
Route::put('/form/{form}', [FormController::class, 'update'])->name('form.update');
Route::delete('/form/{form}', [FormController::class, 'destroy'])->name('form.destroy');


Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('private');
    });
});
