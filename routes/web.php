<?php

use App\Http\Controllers\RouteController;
use Illuminate\Support\Facades\App;
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

Route::get('/lang/{locale}', [RouteController::class, 'switchLang'])->name('lang.switch');

Route::get('/', [RouteController::class, 'home'])->name('home');

Route::get('/lstm', [RouteController::class, 'lstm'])->name('lstm');

Route::get('/gru', [RouteController::class, 'gru'])->name('gru');
