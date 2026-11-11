<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buyers', function (Blueprint $table) {
            $table->id();
            $table->string('legal_name');
            $table->string('display_name')->nullable();
            $table->string('buyer_category')->default('recovery_company');
            $table->string('website')->nullable();
            $table->string('business_email')->nullable();
            $table->string('business_phone')->nullable();
            $table->string('business_address')->nullable();
            $table->string('verification_status')->default('pending');
            $table->boolean('is_active')->default(false);
            $table->text('internal_notes')->nullable();
            $table->timestamps();
            $table->index(['buyer_category', 'verification_status', 'is_active']);
        });

        Schema::create('buyer_authorizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('us_state_id')->constrained('us_states')->restrictOnDelete();
            $table->string('service_category')->default('unclaimed_property_assistance');
            $table->string('authorization_type')->nullable();
            $table->string('registration_reference')->nullable();
            $table->string('status')->default('review_required');
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('verification_notes')->nullable();
            $table->timestamps();
            $table->unique(['buyer_id', 'us_state_id', 'service_category'], 'buyer_state_service_unique');
        });

        Schema::create('consent_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospect_id')->constrained()->cascadeOnDelete();
            $table->string('phone_e164', 20);
            $table->string('calling_entity_legal_name');
            $table->string('named_seller_legal_name');
            $table->string('consent_scope');
            $table->string('consent_method');
            $table->string('consent_text_version');
            $table->longText('consent_text');
            $table->string('capture_source_url', 2048)->nullable();
            $table->string('evidence_reference')->nullable();
            $table->json('evidence_metadata')->nullable();
            $table->string('verification_status')->default('pending');
            $table->timestamp('captured_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
            $table->index(['phone_e164', 'verification_status']);
            $table->index(['prospect_id', 'revoked_at']);
        });

        Schema::create('dnc_suppressions', function (Blueprint $table) {
            $table->id();
            $table->string('phone_e164', 20)->index();
            $table->string('scope')->default('all_telemarketing');
            $table->string('reason')->default('consumer_request');
            $table->string('source')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('suppressed_at');
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
            $table->index(['phone_e164', 'is_active']);
        });

        Schema::create('calling_eligibility', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospect_id')->constrained()->cascadeOnDelete();
            $table->string('phone_e164', 20);
            $table->foreignId('buyer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('decision')->default('blocked');
            $table->json('reason_codes')->nullable();
            $table->json('evidence_snapshot')->nullable();
            $table->timestamp('evaluated_at');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->index(['prospect_id', 'decision']);
            $table->index(['phone_e164', 'decision']);
        });

        Schema::create('lead_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('us_state_id')->constrained('us_states')->restrictOnDelete();
            $table->foreignId('buyer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('status')->default('draft');
            $table->string('channel')->default('ai_voice');
            $table->unsignedInteger('monthly_appointment_target')->default(100);
            $table->text('description')->nullable();
            $table->timestamp('approved_for_launch_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'us_state_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_campaigns');
        Schema::dropIfExists('calling_eligibility');
        Schema::dropIfExists('dnc_suppressions');
        Schema::dropIfExists('consent_records');
        Schema::dropIfExists('buyer_authorizations');
        Schema::dropIfExists('buyers');
    }
};
