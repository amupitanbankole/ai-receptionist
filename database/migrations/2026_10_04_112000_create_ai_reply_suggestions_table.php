<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_reply_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('sales_conversations')->cascadeOnDelete();
            $table->foreignId('inbound_message_id')->nullable()->constrained('messages')->nullOnDelete();
            $table->string('type')->default('reply');
            $table->string('intent')->nullable();
            $table->string('priority')->nullable();
            $table->string('subject')->nullable();
            $table->longText('body')->nullable();
            $table->text('rationale')->nullable();
            $table->string('status')->default('draft');
            $table->string('provider')->default('openai');
            $table->string('model')->nullable();
            $table->json('metadata')->nullable();
            $table->dateTime('generated_at')->nullable();
            $table->timestamps();

            $table->index(['conversation_id', 'status']);
            $table->index('intent');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_reply_suggestions');
    }
};