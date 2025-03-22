<?php

use App\Http\Controllers\MyController;
use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return view('firebase');
});

Route::get('/post_data', [MyController::class, 'add_record']);