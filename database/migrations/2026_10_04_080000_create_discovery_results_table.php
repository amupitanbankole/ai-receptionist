<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discovery_results', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('industry')->nullable();
            $table->string('city')->nullable();
            $table->string('county')->nullable();
            $table->string('postcode')->nullable();
            $table->string('country')->default('United Kingdom');
            $table->string('source');
            $table->string('source_url')->nullable();
            $table->text('discovery_notes')->nullable();
            $table->string('status')->default('new');
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->timestamps();

            $table->index('status');
            $table->index('source');
            $table->index('company_id');
            $table->index(['company_name', 'city']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discovery_results');
    }
};
