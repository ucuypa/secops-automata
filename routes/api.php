<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VulnerabilityController;

Route::get('/vulnerabilities', [VulnerabilityController::class, 'index']);