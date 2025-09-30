<?php

use Illuminate\Support\Facades\Route;

Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');
// Route::get('/', function () {
//     return response()->json(['message' => 'Laravel API is running.']);
// });
