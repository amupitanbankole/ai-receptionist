<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('appointment_type_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->string('timezone')->default('Europe/London');
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('status')->default('confirmed');
            $table->string('source')->default('ai_receptionist');
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->dateTime('confirmation_sent_at')->nullable();
            $table->timestamps();
            $table->index(['company_id', 'starts_at']);
            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void { Schema::dropIfExists('appointments'); }
};