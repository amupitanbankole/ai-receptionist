<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('receptionist_knowledge', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('general');
            $table->string('title');
            $table->longText('content');
            $table->boolean('active')->default(true);
            $table->unsignedInteger('priority')->default(50);
            $table->timestamps();
            $table->index(['company_id', 'active', 'priority']);
        });
    }

    public function down(): void { Schema::dropIfExists('receptionist_knowledge'); }
};