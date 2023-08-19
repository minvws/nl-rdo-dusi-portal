<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
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
//TODO: when form frontend is being developed
//Route::get('/form', [SubsidyController::class, 'index'])->name('form.index');
//Route::get('/form/create', [SubsidyController::class, 'create'])->name('form.create');
//Route::post('/form', [SubsidyController::class, 'store'])->name('form.store');
//Route::get('/form/{form}', [SubsidyController::class, 'show'])->name('form.show');
//Route::get('/form/{form}/edit', [SubsidyController::class, 'edit'])->name('form.edit');
//Route::put('/form/{form}', [SubsidyController::class, 'update'])->name('form.update');
//Route::delete('/form/{form}', [SubsidyController::class, 'destroy'])->name('form.destroy');


Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('private');
    });
});
