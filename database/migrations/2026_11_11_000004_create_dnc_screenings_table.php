<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dnc_screenings', function (Blueprint $table) {
            $table->id();
            $table->string('phone_e164', 20)->index();
            $table->string('screening_type');
            $table->string('status')->default('unknown');
            $table->string('source')->nullable();
            $table->string('result_reference')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('evidence_metadata')->nullable();
            $table->timestamps();
            $table->index(['phone_e164', 'screening_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dnc_screenings');
    }
};
