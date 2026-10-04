<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        EmailTemplate::updateOrCreate(
            ['name' => 'AI Receptionist — Initial Outreach'],
            [
                'subject' => 'Quick question for {{company_name}}',
                'body' => "Hi {{first_name}},\n\nI came across {{company_name}} and noticed you provide {{industry}} services in {{city}}.\n\nI work on an AI receptionist that answers customer calls, captures leads and can book appointments when your team is busy or unavailable.\n\nWould you be open to a quick 10-minute demo?\n\nBest,\nAI Receptionist Sales Team",
                'is_active' => true,
            ]
        );
    }
}
