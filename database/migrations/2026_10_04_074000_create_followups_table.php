<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('followups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->string('type')->default('manual');
            $table->string('subject');
            $table->text('notes')->nullable();
            $table->dateTime('due_at');
            $table->dateTime('completed_at')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['lead_id', 'status']);
            $table->index('due_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('followups');
    }
};
