<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            // Basic company information
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('website')->nullable();
            $table->string('industry')->nullable();
            $table->string('business_type')->nullable();
            $table->text('description')->nullable();

            // Contact information
            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            // Location
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('county')->nullable();
            $table->string('postcode')->nullable();
            $table->string('country')->default('United Kingdom');

            // Business intelligence
            $table->json('service_areas')->nullable();
            $table->json('business_hours')->nullable();

            // Lead discovery
            $table->string('source')->nullable();
            $table->string('source_url')->nullable();

            // CRM status
            $table->string('status')->default('prospect');
            $table->unsignedTinyInteger('lead_score')->default(0);

            $table->timestamps();

            // Useful indexes
            $table->index('industry');
            $table->index('city');
            $table->index('postcode');
            $table->index('status');
            $table->index('lead_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};