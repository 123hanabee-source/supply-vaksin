<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->file(public_path('vaccine.html'));
});

Route::get('/vaccine.html', function () {
    return response()->file(public_path('vaccine.html'));
});
