<?php

use App\Helpers\Helper;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
//    return redirect()->route('login');
    return view('welcome');
})->name('welcome');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Helper::includeRoutes(__DIR__ . '/web/');
});
