<?php

use Illuminate\Support\Facades\Route;

Route::prefix('V1')->group(function () {
    require __DIR__ . '/admin.php';
    require __DIR__ . '/shop.php';
});
