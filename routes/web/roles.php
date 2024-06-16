<?php

use Livewire\Volt\Volt;
use \Illuminate\Support\Facades\Route;

Route::prefix('roles')->name('roles.')->group(function (){
    Volt::route('index', 'roles/index')->name('index');
});