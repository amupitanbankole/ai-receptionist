<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaign_recipients', function (Blueprint $table) {
            $table->unsignedInteger('current_step')->default(1)->after('status');
            $table->dateTime('next_step_at')->nullable()->after('scheduled_at');
            $table->index(['campaign_id', 'current_step', 'next_step_at']);
        });
    }

    public function down(): void
    {
        Schema::table('campaign_recipients', function (Blueprint $table) {
            $table->dropIndex(['campaign_id', 'current_step', 'next_step_at']);
            $table->dropColumn(['current_step', 'next_step_at']);
        });
    }
};
