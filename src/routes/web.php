<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
});

Route::get('/categories', function () {
    return Inertia::render('Categories');
});

Route::get('/trash', function () {
    return Inertia::render('Trash');
});
