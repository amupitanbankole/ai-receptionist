<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->string('type');
            $table->string('subject');
            $table->text('description')->nullable();
            $table->dateTime('occurred_at')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'lead_id']);
            $table->index('type');
            $table->index('occurred_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
