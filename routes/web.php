<?php

use Illuminate\Support\Facades\Route;

// This catch-all route should be at the bottom of all web routes
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');
