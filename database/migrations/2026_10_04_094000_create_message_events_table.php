<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->cascadeOnDelete();
            $table->string('event_type');
            $table->string('provider_event_id')->nullable();
            $table->json('payload')->nullable();
            $table->dateTime('occurred_at')->nullable();
            $table->timestamps();
            $table->index(['event_type', 'occurred_at']);
            $table->index('provider_event_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_events');
    }
};
