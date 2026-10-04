<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('appointment_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('duration_minutes')->default(30);
            $table->unsignedInteger('buffer_minutes')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->index(['company_id', 'active']);
        });
    }

    public function down(): void { Schema::dropIfExists('appointment_types'); }
};