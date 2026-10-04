<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyIntelligence extends Model
{
    protected $table = 'company_intelligence';

    use HasFactory;

    protected $fillable = [
        'company_id','research_status','website_title','research_summary','services_summary',
        'service_areas_summary','booking_process','sales_opportunities','pain_points',
        'strengths','technology_notes','research_data','researched_at',
    ];

    protected $casts = [
        'research_data' => 'array',
        'researched_at' => 'datetime',
    ];

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
}
