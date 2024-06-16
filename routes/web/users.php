<?php

use Livewire\Volt\Volt;
use \Illuminate\Support\Facades\Route;

Route::prefix('users')->name('users.')->group(function (){
    Volt::route('index', 'users/index')->name('index');
});