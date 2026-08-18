<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VulnerabilityController;
use App\Http\Controllers\ScanController;

Route::get('/vulnerabilities', [VulnerabilityController::class, 'index']);
Route::get('/scan', [ScanController::class, 'launch']);