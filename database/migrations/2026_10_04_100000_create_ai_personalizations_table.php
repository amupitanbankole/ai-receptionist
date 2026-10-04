<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_personalizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->default('outreach');
            $table->string('provider')->default('openai');
            $table->string('model')->nullable();
            $table->text('subject')->nullable();
            $table->longText('content')->nullable();
            $table->longText('prompt')->nullable();
            $table->string('status')->default('generated');
            $table->json('metadata')->nullable();
            $table->dateTime('generated_at')->nullable();
            $table->timestamps();
            $table->index(['lead_id', 'type']);
            $table->index(['status', 'generated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_personalizations');
    }
};
