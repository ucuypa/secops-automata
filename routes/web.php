<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScanController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/scan/launch', [ScanController::class, 'launch']);
