<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->foreignId('lead_source_id')
                ->nullable()
                ->after('contact_id')
                ->constrained('lead_sources')
                ->nullOnDelete();

            $table->index('lead_source_id');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['lead_source_id']);
            $table->dropIndex(['lead_source_id']);
            $table->dropColumn('lead_source_id');
        });
    }
};
