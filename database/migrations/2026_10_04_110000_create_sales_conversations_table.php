<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete();
            $table->string('channel')->default('email');
            $table->string('subject')->nullable();
            $table->string('status')->default('open');
            $table->string('intent')->nullable();
            $table->string('priority')->default('normal');
            $table->string('next_action')->nullable();
            $table->text('summary')->nullable();
            $table->dateTime('last_message_at')->nullable();
            $table->dateTime('last_inbound_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['status', 'priority']);
            $table->index(['lead_id', 'status']);
            $table->index('last_message_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_conversations');
    }
};