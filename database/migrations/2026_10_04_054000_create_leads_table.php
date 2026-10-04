<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->foreignId('contact_id')
                ->nullable()
                ->constrained('contacts')
                ->nullOnDelete();

            $table->string('title');
            $table->string('status')->default('new');
            $table->unsignedTinyInteger('score')->default(0);
            $table->string('temperature')->default('low');
            $table->string('source')->nullable();
            $table->text('notes')->nullable();
            $table->dateTime('next_follow_up_at')->nullable();

            $table->timestamps();

            $table->index('company_id');
            $table->index('contact_id');
            $table->index('status');
            $table->index('score');
            $table->index('temperature');
            $table->index('next_follow_up_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
