<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_intelligence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->unique()->constrained('companies')->cascadeOnDelete();
            $table->string('research_status')->default('pending');
            $table->string('website_title')->nullable();
            $table->text('research_summary')->nullable();
            $table->text('services_summary')->nullable();
            $table->text('service_areas_summary')->nullable();
            $table->text('booking_process')->nullable();
            $table->text('sales_opportunities')->nullable();
            $table->text('pain_points')->nullable();
            $table->text('strengths')->nullable();
            $table->text('technology_notes')->nullable();
            $table->json('research_data')->nullable();
            $table->dateTime('researched_at')->nullable();
            $table->timestamps();

            $table->index('research_status');
            $table->index('researched_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_intelligence');
    }
};
