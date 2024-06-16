<?php

use Livewire\Volt\Volt;
use \Illuminate\Support\Facades\Route;

Route::prefix('assessments')->name('assessments.')->group(function (){
    Volt::route('index', 'assessments/index')->name('index');
    Volt::route('view/{id}', 'assessments/view')->name('view');
    Volt::route('instructions/{id}', 'assessments/instructions')->name('instructions');
    Volt::route('start-assessment/{id}', 'assessments/start-assessment')->name('start');
    Volt::route('task/{id}', 'assessments/identification-task')->name('task');
    Volt::route('task-preview/{id}', 'assessments/preview-task')->name('preview');

    Volt::route('read-task/{id}', 'assessments/read-task')->name('read');
    Volt::route('attempt-task/{id}', 'assessments/answer-task')->name('answer');
});