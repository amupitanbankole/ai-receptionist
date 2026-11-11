<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('call_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospect_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('calling_eligibility_id')->nullable()->constrained('calling_eligibility')->nullOnDelete();
            $table->string('phone_e164', 20);
            $table->string('provider_call_id')->nullable()->unique();
            $table->string('status')->default('not_started');
            $table->string('outcome')->nullable();
            $table->string('provider')->nullable();
            $table->string('recording_reference')->nullable();
            $table->string('transcript_reference')->nullable();
            $table->text('summary')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
            $table->index(['prospect_id', 'status']);
        });

        Schema::create('recovery_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospect_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained()->restrictOnDelete();
            $table->foreignId('lead_campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('call_attempt_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('pending_confirmation');
            $table->timestamp('starts_at');
            $table->string('timezone')->default('America/New_York');
            $table->string('meeting_method')->default('phone');
            $table->string('buyer_meeting_reference')->nullable();
            $table->timestamp('prospect_confirmed_at')->nullable();
            $table->timestamp('data_sharing_consent_at')->nullable();
            $table->string('data_sharing_consent_buyer_legal_name')->nullable();
            $table->string('data_sharing_consent_version')->nullable();
            $table->timestamp('shared_with_buyer_at')->nullable();
            $table->decimal('agreed_lead_price', 10, 2)->nullable();
            $table->char('currency', 3)->default('USD');
            $table->text('qualification_notes')->nullable();
            $table->timestamps();
            $table->index(['buyer_id', 'starts_at']);
            $table->index(['status', 'starts_at']);
        });

        Schema::create('lead_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recovery_appointment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained()->restrictOnDelete();
            $table->string('status')->default('pending');
            $table->string('delivery_reference')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
            $table->unique(['recovery_appointment_id', 'buyer_id'], 'appointment_buyer_delivery_unique');
            $table->index(['buyer_id', 'status']);
        });

        Schema::create('unclaimed_funds_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('actor_user_id')->nullable();
            $table->string('event');
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('metadata')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['subject_type', 'subject_id']);
            $table->index(['event', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unclaimed_funds_audit_logs');
        Schema::dropIfExists('lead_deliveries');
        Schema::dropIfExists('recovery_appointments');
        Schema::dropIfExists('call_attempts');
    }
};
