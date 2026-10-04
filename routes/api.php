<?php

use App\Http\Controllers\SalesConversationController;
use App\Http\Controllers\ReceptionistController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/inbound-email', [SalesConversationController::class, 'webhook'])
    ->name('api.sales-conversations.webhook');

Route::post('/receptionist/message', [ReceptionistController::class, 'webhook'])
    ->name('api.receptionist.webhook');
