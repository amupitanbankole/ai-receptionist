<?php

use App\Http\Controllers\SalesConversationController;
use App\Http\Controllers\ReceptionistController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/inbound-email', [SalesConversationController::class, 'webhook'])
    ->name('api.sales-conversations.webhook');
\nRoute::post('/receptionist/message', [ReceptionistController::class, 'webhook'])\n    ->name('api.receptionist.webhook');\n