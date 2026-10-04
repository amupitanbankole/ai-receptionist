<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceptionistConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id','enabled','display_name','greeting','tone','instructions','services',
        'service_areas','business_hours','booking_enabled','after_hours_message',
        'escalation_phone','escalation_email','notification_email','timezone','metadata',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'services' => 'array',
        'service_areas' => 'array',
        'business_hours' => 'array',
        'booking_enabled' => 'boolean',
        'metadata' => 'array',
    ];

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
}