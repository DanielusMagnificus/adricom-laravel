<?php

use App\Http\Controllers\CurrencyController;
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

Route::prefix('/currencies')->group(function() {
    Route::get('/', [
    CurrencyController::class, 'index'
    ])->name('index');

    Route::get('/top', [
        CurrencyController::class, 'topCurrencies'
    ])->name('topCurrencies');

    Route::patch('/update/{id}', [
        CurrencyController::class, 'update'
    ])->name('currencyUpdate');
});


