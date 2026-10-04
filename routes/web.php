<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])
    ->name('dashboard');

Route::resource('companies', CompanyController::class);