<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->boolean('emergency_service')->default(false)->after('lead_score');
            $table->boolean('appointment_based')->default(false)->after('emergency_service');
            $table->boolean('phone_prominent')->default(false)->after('appointment_based');
            $table->boolean('online_booking')->default(false)->after('phone_prominent');
            $table->boolean('small_team')->default(false)->after('online_booking');
            $table->boolean('outside_hours_service')->default(false)->after('small_team');
            $table->boolean('high_value_service')->default(false)->after('outside_hours_service');
            $table->boolean('live_chat')->default(false)->after('high_value_service');
            $table->boolean('multiple_locations')->default(false)->after('live_chat');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'emergency_service',
                'appointment_based',
                'phone_prominent',
                'online_booking',
                'small_team',
                'outside_hours_service',
                'high_value_service',
                'live_chat',
                'multiple_locations',
            ]);
        });
    }
};
