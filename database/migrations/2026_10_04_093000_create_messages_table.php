<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_recipient_id')->nullable()->constrained()->nullOnDelete();
            $table->string('direction')->default('outbound');
            $table->string('channel')->default('email');
            $table->string('to_address');
            $table->string('from_address')->nullable();
            $table->string('reply_to')->nullable();
            $table->string('subject')->nullable();
            $table->longText('body')->nullable();
            $table->string('status')->default('queued');
            $table->string('provider_message_id')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['channel', 'status']);
            $table->index('provider_message_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
