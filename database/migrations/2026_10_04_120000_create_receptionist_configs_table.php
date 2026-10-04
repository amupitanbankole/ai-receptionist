<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('receptionist_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('enabled')->default(true);
            $table->string('display_name')->nullable();
            $table->string('greeting')->nullable();
            $table->string('tone')->default('professional');
            $table->text('instructions')->nullable();
            $table->json('services')->nullable();
            $table->json('service_areas')->nullable();
            $table->json('business_hours')->nullable();
            $table->boolean('booking_enabled')->default(true);
            $table->text('after_hours_message')->nullable();
            $table->string('escalation_phone')->nullable();
            $table->string('escalation_email')->nullable();
            $table->string('notification_email')->nullable();
            $table->string('timezone')->default('Europe/London');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('receptionist_configs'); }
};