<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('us_states', function (Blueprint $table) {
            $table->id();
            $table->char('code', 2)->unique();
            $table->string('name')->unique();
            $table->string('finder_status')->default('review_required');
            $table->string('calling_status')->default('blocked');
            $table->text('compliance_notes')->nullable();
            $table->string('legal_review_reference')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('property_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('us_state_id')->nullable()->constrained('us_states')->nullOnDelete();
            $table->string('name');
            $table->string('official_url', 2048);
            $table->string('source_type')->default('state_registry');
            $table->string('access_method')->default('manual');
            $table->boolean('terms_reviewed')->default(false);
            $table->timestamp('terms_reviewed_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['us_state_id', 'is_active']);
        });

        Schema::create('prospects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('us_state_id')->nullable()->constrained('us_states')->nullOnDelete();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_e164', 20)->nullable()->index();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country_code', 2)->default('US');
            $table->string('status')->default('uncontacted');
            $table->timestamp('public_record_last_verified_at')->nullable();
            $table->timestamp('do_not_contact_at')->nullable();
            $table->text('internal_notes')->nullable();
            $table->timestamps();
            $table->index(['us_state_id', 'status']);
            $table->index(['last_name', 'first_name']);
        });

        Schema::create('property_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospect_id')->constrained()->cascadeOnDelete();
            $table->foreignId('us_state_id')->constrained('us_states')->restrictOnDelete();
            $table->foreignId('property_source_id')->nullable()->constrained('property_sources')->nullOnDelete();
            $table->string('external_reference')->nullable();
            $table->string('owner_name');
            $table->string('property_type')->nullable();
            $table->string('reported_holder')->nullable();
            $table->decimal('reported_amount', 14, 2)->nullable();
            $table->char('currency', 3)->default('USD');
            $table->string('official_record_url', 2048);
            $table->string('verification_status')->default('unverified');
            $table->timestamp('source_recorded_at')->nullable();
            $table->timestamp('last_verified_at')->nullable();
            $table->json('source_metadata')->nullable();
            $table->timestamps();
            $table->index(['us_state_id', 'verification_status']);
            $table->index(['prospect_id', 'external_reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_records');
        Schema::dropIfExists('prospects');
        Schema::dropIfExists('property_sources');
        Schema::dropIfExists('us_states');
    }
};
