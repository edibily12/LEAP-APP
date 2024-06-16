<?php

use Livewire\Volt\Volt;
use \Illuminate\Support\Facades\Route;

Route::prefix('students')->name('students.')->group(function (){
    Volt::route('index', 'students/index')->name('index');
    Volt::route('view/{id}', 'students/view')->name('view');
    Volt::route('analysis', 'students/analyze')->name('analyze');
});