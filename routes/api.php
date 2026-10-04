<?php

use App\Http\Controllers\ReceptionistController;
use App\Http\Controllers\ReceptionistWidgetController;
use App\Http\Controllers\SalesConversationController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/inbound-email', [SalesConversationController::class, 'webhook'])
    ->name('api.sales-conversations.webhook');

Route::post('/receptionist/message', [ReceptionistController::class, 'webhook'])
    ->name('api.receptionist.webhook');

Route::get('/receptionist/widget', [ReceptionistWidgetController::class, 'config'])
    ->middleware('throttle:60,1')
    ->name('api.receptionist.widget.config');

Route::post('/receptionist/chat', [ReceptionistWidgetController::class, 'chat'])
    ->middleware('throttle:30,1')
    ->name('api.receptionist.widget.chat');

Route::options('/receptionist/{action}', [ReceptionistWidgetController::class, 'options'])
    ->where('action', 'widget|chat')
    ->name('api.receptionist.widget.options');
