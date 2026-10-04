<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FollowupController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadScoringController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('companies', CompanyController::class);
Route::resource('contacts', ContactController::class);
Route::resource('leads', LeadController::class);

Route::get('/lead-scoring', [LeadScoringController::class, 'index'])->name('lead-scoring.index');
Route::post('/lead-scoring/run', [LeadScoringController::class, 'run'])->name('lead-scoring.run');
Route::patch('/lead-scoring/companies/{company}/signals', [LeadScoringController::class, 'updateSignals'])->name('lead-scoring.company.signals');

Route::resource('activities', ActivityController::class)->only(['index','create','store']);
Route::resource('followups', FollowupController::class)->only(['index','create','store']);
Route::patch('/followups/{followup}/complete', [FollowupController::class, 'complete'])->name('followups.complete');


Route::resource('discovery', \App\Http\Controllers\DiscoveryResultController::class)->only(['index','create','store']);
Route::post('/discovery/{result}/promote', [\App\Http\Controllers\DiscoveryResultController::class, 'promote'])->name('discovery.promote');
Route::patch('/discovery/{result}/discard', [\App\Http\Controllers\DiscoveryResultController::class, 'discard'])->name('discovery.discard');
Route::get('/companies/{company}/research', [\App\Http\Controllers\CompanyResearchController::class, 'show'])->name('research.show');
Route::post('/companies/{company}/research', [\App\Http\Controllers\CompanyResearchController::class, 'run'])->name('research.run');
Route::patch('/companies/{company}/research', [\App\Http\Controllers\CompanyResearchController::class, 'update'])->name('research.update');
