<?php

namespace Database\Seeders;

use App\Models\LeadSource;
use Illuminate\Database\Seeder;

class LeadSourceSeeder extends Seeder
{
    public function run(): void
    {
        $sources = [
            ['name' => 'Google Search', 'slug' => 'google-search', 'description' => 'Prospect discovered through Google search results.'],
            ['name' => 'LinkedIn', 'slug' => 'linkedin', 'description' => 'Prospect discovered through LinkedIn.'],
            ['name' => 'Website Research', 'slug' => 'website-research', 'description' => 'Prospect identified through company website research.'],
            ['name' => 'Referral', 'slug' => 'referral', 'description' => 'Prospect referred by another person or business.'],
            ['name' => 'Manual Entry', 'slug' => 'manual-entry', 'description' => 'Prospect added manually by a team member.'],
            ['name' => 'Import', 'slug' => 'import', 'description' => 'Prospect imported from an external data source.'],
            ['name' => 'Other', 'slug' => 'other', 'description' => 'Other acquisition source.'],
        ];

        foreach ($sources as $source) {
            LeadSource::updateOrCreate(
                ['slug' => $source['slug']],
                $source + ['is_active' => true]
            );
        }
    }
}
