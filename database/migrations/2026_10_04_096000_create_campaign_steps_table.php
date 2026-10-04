<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('step_number')->default(1);
            $table->unsignedInteger('day_offset')->default(0);
            $table->string('subject');
            $table->longText('body');
            $table->timestamps();
            $table->unique(['campaign_id', 'step_number']);
            $table->index(['campaign_id', 'day_offset']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_steps');
    }
};
