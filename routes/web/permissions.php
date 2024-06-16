<?php

use Livewire\Volt\Volt;
use \Illuminate\Support\Facades\Route;

Route::prefix('permissions')->name('permissions.')->group(function (){
    Volt::route('index', 'permissions/index')->name('index');
});