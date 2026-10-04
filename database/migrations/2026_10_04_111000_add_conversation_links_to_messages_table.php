<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->foreignId('conversation_id')->nullable()->after('campaign_recipient_id')->constrained('sales_conversations')->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->after('conversation_id')->constrained()->nullOnDelete();
            $table->foreignId('company_id')->nullable()->after('lead_id')->constrained()->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->after('company_id')->constrained()->nullOnDelete();
            $table->index(['conversation_id', 'direction']);
            $table->index(['lead_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['conversation_id']);
            $table->dropForeign(['lead_id']);
            $table->dropForeign(['company_id']);
            $table->dropForeign(['contact_id']);
            $table->dropIndex(['conversation_id', 'direction']);
            $table->dropIndex(['lead_id', 'created_at']);
            $table->dropColumn(['conversation_id', 'lead_id', 'company_id', 'contact_id']);
        });
    }
};