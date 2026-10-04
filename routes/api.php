<?php

use App\Http\Controllers\SalesConversationController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/inbound-email', [SalesConversationController::class, 'webhook'])
    ->name('api.sales-conversations.webhook');
