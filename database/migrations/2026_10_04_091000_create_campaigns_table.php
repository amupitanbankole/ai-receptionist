<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status')->default('draft');
            $table->foreignId('template_id')->nullable()->constrained('email_templates')->nullOnDelete();
            $table->string('subject')->nullable();
            $table->longText('body')->nullable();
            $table->unsignedInteger('daily_limit')->default(25);
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->text('unsubscribe_text')->nullable();
            $table->string('reply_to')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->index('status');
            $table->index('scheduled_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
