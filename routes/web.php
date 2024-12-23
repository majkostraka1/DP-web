<?php

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

Route::get('/lang/{locale}', function ($locale) {
    $allowed = ['cz', 'sk', 'en'];

    if (! in_array($locale, $allowed)) {
        $locale = 'en';
    }

    App::setLocale($locale);
    session(['locale' => $locale]);

    return redirect()->back();
})->name('lang.switch');



Route::get('/', function () {
    return view('home');
});

